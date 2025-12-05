import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/venta"
AUTH = HTTPBasicAuth("tester", "Passw0rd!")
TIMEOUT = 30

def test_post_ventas_agregarventa_create_sale():
    try:
        # Step 1: Verify server is responding (GET /productos)
        resp = requests.get(f"{BASE_URL}/productos", auth=AUTH, timeout=TIMEOUT)
        assert resp.status_code == 200, f"Server not responding for products endpoint, status: {resp.status_code}"
        productos = resp.json()
        assert isinstance(productos, list) and len(productos) > 0, "No products available to test with."

        # Select first product with available stock > 0
        product = None
        for p in productos:
            if p.get("stock", 0) > 0:
                product = p
                break
        assert product, "No product with stock available for sale."

        product_id = product["id"]
        initial_stock = product["stock"]

        # Step 2: Verify customers exist or create one
        resp = requests.get(f"{BASE_URL}/clientes", auth=AUTH, timeout=TIMEOUT)
        assert resp.status_code == 200, f"Failed retrieving customers, status: {resp.status_code}"
        clientes = resp.json()
        if not clientes:
            # Create a new customer
            new_customer_data = {
                "nombre": "Test Customer",
                "correo": "testcustomer@example.com",
                "telefono": "5551234567",
                "direccion": "123 Test St"
            }
            resp_cust_create = requests.post(f"{BASE_URL}/clientes", json=new_customer_data, auth=AUTH, timeout=TIMEOUT)
            assert resp_cust_create.status_code == 201, "Failed to create test customer"
            cliente = resp_cust_create.json()
            cliente_id = cliente.get("id")
        else:
            cliente_id = clientes[0].get("id")

        # Step 3: Check if caja (cash register) is open (simulate by checking /ventas or assume open)
        # No direct endpoint given; continuing assuming caja is open.

        # Step 4: Prepare sale data and POST to /ventas/agregarVenta
        cantidad_a_vender = 1
        request_payload = {
            "productos": [
                {
                    "id": product_id,
                    "cantidad": cantidad_a_vender
                }
            ],
            "cliente_id": cliente_id,
            "metodo_pago": "efectivo"
        }

        resp_sale = requests.post(
            f"{BASE_URL}/ventas/agregarVenta",
            json=request_payload,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert resp_sale.status_code == 200, f"Sale creation failed with status {resp_sale.status_code}"
        sale_response = resp_sale.json()
        assert "venta_id" in sale_response, "Response missing venta_id"

        venta_id = sale_response["venta_id"]

        # Step 5: Verify stock updated (GET /productos to confirm stock reduced)
        resp_producto_post_sale = requests.get(f"{BASE_URL}/productos/{product_id}", auth=AUTH, timeout=TIMEOUT)
        if resp_producto_post_sale.status_code == 200:
            producto_post_sale = resp_producto_post_sale.json()
            post_stock = producto_post_sale.get("stock")
            assert post_stock == (initial_stock - cantidad_a_vender), f"Stock did not update correctly: before {initial_stock}, after {post_stock}"
        else:
            # If single product GET not supported fallback to listing all
            resp_productos = requests.get(f"{BASE_URL}/productos", auth=AUTH, timeout=TIMEOUT)
            assert resp_productos.status_code == 200, "Failed to get products to verify stock update"
            productos_post = resp_productos.json()
            post_stock = None
            for p in productos_post:
                if p.get("id") == product_id:
                    post_stock = p.get("stock")
                    break
            assert post_stock == (initial_stock - cantidad_a_vender), f"Stock did not update correctly after sale."

    finally:
        # Cleanup: delete the created sale if possible to restore original state
        if 'venta_id' in locals():
            del_payload = {"venta_id": venta_id}
            resp_del = requests.delete(f"{BASE_URL}/ventas/deleteVenta", json=del_payload, auth=AUTH, timeout=TIMEOUT)
            # If delete returns 200 or 204, consider success; if not, maybe log or raise
            assert resp_del.status_code in (200,204), f"Failed to delete test sale with status {resp_del.status_code}"
        # Cleanup created customer if was created
        if 'resp_cust_create' in locals() and cliente_id:
            resp_del_cust = requests.delete(f"{BASE_URL}/clientes", json={"id": cliente_id}, auth=AUTH, timeout=TIMEOUT)
            assert resp_del_cust.status_code in (200,204), f"Failed to delete test customer with status {resp_del_cust.status_code}"

test_post_ventas_agregarventa_create_sale()
