import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost/venta"
USERNAME = "tester"
PASSWORD = "Passw0rd!"
TIMEOUT = 30


def test_post_ventas_cantidadventa_update_sale_quantity():
    auth = HTTPBasicAuth(USERNAME, PASSWORD)
    headers = {"Accept": "application/json"}

    # Step 1: Check server responsiveness with basic auth by a GET on /ventas (should return 200)
    resp_check = requests.get(f"{BASE_URL}/ventas", auth=auth, headers=headers, timeout=TIMEOUT)
    assert resp_check.status_code == 200, f"Server check failed with status code {resp_check.status_code}"

    # Step 2: Get products
    products_resp = requests.get(f"{BASE_URL}/productos", auth=auth, headers=headers, timeout=TIMEOUT)
    assert products_resp.status_code == 200, f"Failed to list products, status {products_resp.status_code}"
    products = []
    try:
        products = products_resp.json()
    except Exception:
        pass

    if not products:
        product_payload = {
            "nombre": "Test Product",
            "descripcion": "Product for testing",
            "precio": 100.0,
            "stock": 50,
            "categoria_id": 1,
            "unidad_medida_id": 1
        }
        prod_create_resp = requests.post(f"{BASE_URL}/productos", auth=auth, json=product_payload, headers=headers, timeout=TIMEOUT)
        assert prod_create_resp.status_code == 201, f"Failed to create product, status {prod_create_resp.status_code}"
        try:
            product = prod_create_resp.json()
        except Exception:
            assert False, "Failed to parse JSON creating product"
    else:
        product = products[0]
        # Ensure stock > 0
        if product.get("stock", 0) <= 0:
            product_update_payload = product.copy()
            product_update_payload["stock"] = 50
            prod_update_resp = requests.put(f"{BASE_URL}/productos", auth=auth, json=product_update_payload, headers=headers, timeout=TIMEOUT)
            assert prod_update_resp.status_code == 200, f"Failed to update product stock for testing, status {prod_update_resp.status_code}"

    product_id = product.get("id")
    assert product_id is not None, "Product ID not available"

    # Step 3: Create a temporary sale via POST /ventas/agregarVenta
    sale_add_payload = {
        "producto_id": product_id,
        "cantidad": 1
    }
    sale_add_resp = requests.post(f"{BASE_URL}/ventas/agregarVenta", auth=auth, json=sale_add_payload, headers=headers, timeout=TIMEOUT)
    assert sale_add_resp.status_code == 200, f"Failed to add product to sale, status {sale_add_resp.status_code}"

    # Parse JSON safely
    try:
        sale_temp_data = sale_add_resp.json()
    except Exception:
        assert False, "Response from agregarVenta is not valid JSON"

    # Extract sale detail id
    venta_id = sale_temp_data.get("venta_id") or sale_temp_data.get("id") or sale_temp_data.get("detalle_venta_id")
    if not venta_id and isinstance(sale_temp_data, dict):
        # Sometimes the sale info is in a data key
        data_val = sale_temp_data.get("data")
        if isinstance(data_val, dict):
            venta_id = data_val.get("venta_id") or data_val.get("id") or data_val.get("detalle_venta_id")

    assert venta_id, "Sale detail ID not returned from agregarVenta"

    # Step 4: Update sale quantity
    update_quantity_payload = {
        "detalle_id": venta_id,
        "cantidad": 2
    }
    update_resp = requests.post(f"{BASE_URL}/ventas/cantidadVenta", auth=auth, json=update_quantity_payload, headers=headers, timeout=TIMEOUT)

    assert update_resp.status_code == 200, f"Failed to update sale quantity, status {update_resp.status_code}"

    try:
        update_data = update_resp.json()
    except Exception:
        assert False, "Update quantity response is not valid JSON"

    assert "cantidad" in update_data, "Response missing updated quantity"
    assert update_data["cantidad"] == 2, f"Quantity not updated correctly, expected 2 got {update_data['cantidad']}"
    assert "stock_disponible" in update_data, "Response missing stock availability info"
    assert isinstance(update_data["stock_disponible"], int) or isinstance(update_data["stock_disponible"], float), "stock_disponible must be a number"
    assert update_data["stock_disponible"] >= 0, "Stock availability negative, invalid"

    # Step 5: Try updating quantity to exceed stock
    excessive_quantity_payload = {
        "detalle_id": venta_id,
        "cantidad": 9999
    }
    excessive_resp = requests.post(f"{BASE_URL}/ventas/cantidadVenta", auth=auth, json=excessive_quantity_payload, headers=headers, timeout=TIMEOUT)

    assert excessive_resp.status_code in (400, 422), f"Expected error status when exceeding stock but got {excessive_resp.status_code}"
    try:
        excessive_data = excessive_resp.json()
        # Expecting error message or validation details
        assert "error" in excessive_data or "message" in excessive_data or "errors" in excessive_data, "Expected error details in response when exceeding stock"
    except Exception:
        pass  # If no JSON returned, still valid error

    # Cleanup: Delete product if created
    if not products:
        prod_del_resp = requests.delete(f"{BASE_URL}/productos", auth=auth, json={"id": product_id}, headers=headers, timeout=TIMEOUT)
        assert prod_del_resp.status_code == 200, f"Failed to cleanup test product, status {prod_del_resp.status_code}"


test_post_ventas_cantidadventa_update_sale_quantity()
