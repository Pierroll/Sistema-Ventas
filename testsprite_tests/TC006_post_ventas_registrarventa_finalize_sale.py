import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/venta"
AUTH_USERNAME = "tester"
AUTH_PASSWORD = "Passw0rd!"


def test_post_ventas_registrarventa_finalize_sale():
    session = requests.Session()
    session.auth = HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD)
    headers = {"Content-Type": "application/json"}
    timeout = 30

    try:
        # 1. Verify the server is responding with 200 for auth/login endpoint with credentials in JSON
        login_payload = {"username": AUTH_USERNAME, "password": AUTH_PASSWORD}
        login_resp = session.post(f"{BASE_URL}/login", json=login_payload, timeout=timeout)
        assert login_resp.status_code == 200, f"Login endpoint did not respond with 200 OK, got {login_resp.status_code}"

        # 2. Verify the cash register is open (GET /caja to check status)
        caja_resp = session.get(f"{BASE_URL}/caja", timeout=timeout)
        assert caja_resp.status_code == 200, f"Caja endpoint did not respond with 200 OK, got {caja_resp.status_code}"
        caja_data = caja_resp.json()
        caja_abierta = any(c.get("estado") == "abierta" or c.get("estado") == "open" for c in caja_data) if isinstance(caja_data, list) else caja_data.get("estado") == "abierta" or caja_data.get("estado") == "open"
        assert caja_abierta, "Cash register is not open. Cannot finalize sale."

        # 3. Create a test product with stock to ensure stock availability
        prod_payload = {
            "nombre_producto": "ProductoTest Finalize Sale",
            "categoria_id": 1,
            "unidad_medida_id": 1,
            "precio_venta": 100.0,
            "stock": 10,
            "descripcion": "Producto para prueba automatizada",
            "estado": 1
        }
        prod_resp = session.post(f"{BASE_URL}/productos", json=prod_payload, headers=headers, timeout=timeout)
        assert prod_resp.status_code in (200,201), f"Failed to create product, status: {prod_resp.status_code}"
        producto = prod_resp.json()
        producto_id = producto.get("id") or producto.get("producto_id")
        assert producto_id is not None, "Product ID not returned."

        # 4. Create a temp sale with product and quantity using POST /ventas/agregarVenta
        agregarventa_payload = {
            "producto_id": producto_id,
            "cantidad": 2
        }
        add_venta_resp = session.post(f"{BASE_URL}/ventas/agregarVenta", json=agregarventa_payload, headers=headers, timeout=timeout)
        assert add_venta_resp.status_code == 200, f"Failed to add product to sale, status: {add_venta_resp.status_code}"
        add_venta_data = add_venta_resp.json()
        id_detalle_temp = add_venta_data.get("id") or add_venta_data.get("detalle_temp_id")
        assert id_detalle_temp is not None, "Detalle_temp ID not returned after adding sale item."

        # 5. Finalize sale with POST /ventas/registrarVenta using the temp sale data
        registrarventa_payload = {
            "cliente_id": None,
            "metodo_pago": "efectivo",
            "usuario_id": AUTH_USERNAME,  # added user info
            "detalles_venta": [
                {"producto_id": producto_id, "cantidad": 2}
            ]
        }

        reg_venta_resp = session.post(f"{BASE_URL}/ventas/registrarVenta", json=registrarventa_payload, headers=headers, timeout=timeout)
        assert reg_venta_resp.status_code == 200, f"Failed to register/finalize sale, status: {reg_venta_resp.status_code}"
        reg_venta_data = reg_venta_resp.json()
        id_venta = reg_venta_data.get("venta_id") or reg_venta_data.get("id")
        assert id_venta is not None, "Sale ID not returned after finalizing sale."
        # Check receipt and invoice generation flags or URLs if provided
        assert ("factura_url" in reg_venta_data or "recibo_url" in reg_venta_data or "pdf_url" in reg_venta_data), \
            "Invoice or receipt PDF not generated or URL missing."

        # Validate stock update by retrieving product details again
        prod_check_resp = session.get(f"{BASE_URL}/productos?id={producto_id}", timeout=timeout)
        assert prod_check_resp.status_code == 200, f"Failed to fetch product after sale, status: {prod_check_resp.status_code}"
        prod_check_data = prod_check_resp.json()
        # Support both list or dict response
        prod_info = (prod_check_data[0] if isinstance(prod_check_data, list) and len(prod_check_data)>0 else prod_check_data)
        stock_after_sale = prod_info.get("stock")
        assert stock_after_sale == 8, f"Stock after sale should be reduced by 2. Expected 8, got {stock_after_sale}"

    finally:
        # Cleanup: Delete the product created for this test & the sale if possible
        try:
            if 'id_venta' in locals():
                session.post(f"{BASE_URL}/ventas/anularVenta", json={"venta_id": id_venta}, headers=headers, timeout=timeout)
        except Exception:
            pass
        try:
            if 'producto_id' in locals():
                session.delete(f"{BASE_URL}/productos", json={"id": producto_id}, headers=headers, timeout=timeout)
        except Exception:
            pass

test_post_ventas_registrarventa_finalize_sale()
