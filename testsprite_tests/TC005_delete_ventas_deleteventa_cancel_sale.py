import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH = HTTPBasicAuth("tester", "Passw0rd!")
TIMEOUT = 30

def test_delete_ventas_deleteventa_cancel_sale():
    headers = {
        "Accept": "application/json",
        "Content-Type": "application/json"
    }
    sale_id = None
    product_id = None
    product_initial_stock = None

    try:
        # Step 1: Verify server availability (basic GET /ventas)
        r = requests.get(f"{BASE_URL}/ventas", auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert r.status_code == 200, f"Server not responding properly: {r.status_code}"
        content = r.content.strip()
        assert content, "Empty response for /ventas"

        # Step 2: Get products to use for the sale (to create a sale)
        r = requests.get(f"{BASE_URL}/productos", auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert r.status_code == 200, f"Failed to get products: {r.status_code}"
        content = r.content.strip()
        assert content, "Empty response for /productos"
        products = r.json()
        assert isinstance(products, list) and len(products) > 0, "No products available to create a sale"
        product = products[0]
        product_id = product.get("id") or product.get("codigo") or product.get("idProducto") or product.get("id_producto")
        assert product_id is not None, "Product ID not found in product"

        # Get the stock of the product before creating sale
        product_initial_stock = product.get("stock") if "stock" in product else None
        assert product_initial_stock is not None, "Product stock info not available"

        # Step 3: Create a new sale (POST /ventas/agregarVenta)
        sale_payload = {
            "productos": [
                {
                    "idProducto": product_id,
                    "cantidad": 1
                }
            ],
            "idCliente": None,
            "metodoPago": "efectivo",
            "descripcion": "Venta para test cancelacion"
        }
        r = requests.post(f"{BASE_URL}/ventas/agregarVenta", json=sale_payload, auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert r.status_code == 200, f"Failed to create sale: {r.status_code}"
        content = r.content.strip()
        assert content, "Empty response after creating sale"
        sale_resp = r.json()
        sale_id = sale_resp.get("idVenta") or sale_resp.get("id_venta") or sale_resp.get("venta_id") or sale_resp.get("id")
        assert sale_id is not None, "Sale ID not returned after creation"

        # Step 4: Cancel the sale via POST /ventas/anularVenta with sale id payload
        cancel_payload = {"idVenta": sale_id}
        r = requests.post(f"{BASE_URL}/ventas/anularVenta", json=cancel_payload, auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert r.status_code == 200, f"Failed to cancel sale: {r.status_code}"
        content = r.content.strip()
        assert content, "Empty response after canceling sale"
        cancel_resp = r.json()
        # Validate the response confirms cancellation
        assert cancel_resp.get("success") is True or cancel_resp.get("estado") == "anulado" or cancel_resp.get("status") == "cancelled", \
            f"Sale cancellation not confirmed in response: {cancel_resp}"

        # Step 5: Verify that stock was restored by re-fetching product stock
        r = requests.get(f"{BASE_URL}/productos", auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert r.status_code == 200
        content = r.content.strip()
        assert content, "Empty response for /productos after cancel"
        products_after_cancel = r.json()
        # Find our product by id
        product_after = next((p for p in products_after_cancel if (p.get("id") == product_id or p.get("codigo") == product_id or p.get("idProducto") == product_id or p.get("id_producto") == product_id)), None)
        assert product_after is not None, "Product not found after sale cancellation"
        restored_stock = product_after.get("stock") if "stock" in product_after else None
        assert restored_stock == product_initial_stock, f"Product stock was not restored after sale cancellation (before: {product_initial_stock}, after: {restored_stock})"

        # Step 6: Verify that further processing of canceled sale is prevented
        r = requests.post(f"{BASE_URL}/ventas/anularVenta", json=cancel_payload, auth=AUTH, headers=headers, timeout=TIMEOUT)
        if r.status_code == 200:
            content = r.content.strip()
            if content:
                resp_json = r.json()
                assert resp_json.get("success") is False or resp_json.get("error") or resp_json.get("message"), \
                    "Canceled sale can be canceled again or further processed, which should be prevented"
            else:
                assert False, "Empty response when attempting to cancel sale a second time"
        else:
            assert r.status_code != 200

    finally:
        pass

test_delete_ventas_deleteventa_cancel_sale()
