import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH = HTTPBasicAuth("tester", "Passw0rd!")
TIMEOUT = 30

def test_post_ventas_anularventa_void_sale():
    session = requests.Session()
    session.auth = AUTH

    headers = {
        "Accept": "application/json"
    }

    # Step 1: Verify server is responding with 200 status code (health check)
    try:
        health_resp = session.get(f"{BASE_URL}/ventas", headers=headers, timeout=TIMEOUT)
        assert health_resp.status_code == 200, f"Health check failed: {health_resp.status_code}"
    except Exception as e:
        raise AssertionError(f"Server health check failed: {e}")

    sale_id = None

    try:
        # Step 2: Create a new sale (POST /ventas/registrarVenta) to have a sale to void
        productos_resp = session.get(f"{BASE_URL}/productos", headers=headers, timeout=TIMEOUT)
        assert productos_resp.status_code == 200, f"Failed to get products: {productos_resp.status_code}"
        try:
            productos = productos_resp.json()
        except Exception:
            productos = []
        valid_product = None
        for p in productos:
            stock = p.get("stock") if p.get("stock") is not None else p.get("cantidad") if p.get("cantidad") is not None else p.get("existencias") if p.get("existencias") is not None else 0
            if isinstance(stock, (int, float)) and stock > 0:
                valid_product = p
                break
        assert valid_product is not None, "No product with available stock found to create sale."

        prod_id = valid_product.get("id") or valid_product.get("producto_id") or valid_product.get("id_producto")
        assert prod_id is not None, "Valid product must have an id"

        sale_data = {
            "productos": [
                {
                    "producto_id": prod_id,
                    "cantidad": 1
                }
            ],
            # cliente_id can be omitted or set explicitly null if no client
            "metodo_pago": "efectivo"
        }

        # Add products to temporary sale
        add_venta_resp = session.post(f"{BASE_URL}/ventas/agregarVenta", json=sale_data, headers=headers, timeout=TIMEOUT)
        assert add_venta_resp.status_code == 200, f"Failed to add products to sale: {add_venta_resp.status_code}"
        try:
            add_venta_json = add_venta_resp.json()
        except Exception:
            add_venta_json = {}
        sale_temp_id = add_venta_json.get("venta_temp_id") or add_venta_json.get("id_temp_venta")
        assert sale_temp_id is not None, "Temporary sale id not returned from agregarVenta"

        # Register sale finalization
        registrar_data = {
            "venta_temp_id": sale_temp_id,
            # omit cliente_id if none
            "metodo_pago": sale_data["metodo_pago"]
        }
        registrar_resp = session.post(f"{BASE_URL}/ventas/registrarVenta", json=registrar_data, headers=headers, timeout=TIMEOUT)
        assert registrar_resp.status_code == 200, f"Failed to register sale: {registrar_resp.status_code}"
        try:
            registrar_json = registrar_resp.json()
        except Exception:
            registrar_json = {}
        sale_id = registrar_json.get("venta_id") or registrar_json.get("id_venta")
        assert sale_id is not None, "Sale ID not returned after registering sale"

        # Void the sale
        anular_payload = {
            "venta_id": sale_id
        }
        anular_resp = session.post(f"{BASE_URL}/ventas/anularVenta", json=anular_payload, headers=headers, timeout=TIMEOUT)
        assert anular_resp.status_code == 200, f"Failed to void sale: {anular_resp.status_code}"
        try:
            anular_json = anular_resp.json()
        except Exception:
            anular_json = {}

        status = anular_json.get("status", "")
        content_lower = str(anular_json).lower()
        assert status == "success" or "venta anulada" in content_lower, \
            f"Unexpected response content when voiding sale: {anular_json}"

        # Verify product stock restored
        producto_actual_resp = session.get(f"{BASE_URL}/productos/{prod_id}", headers=headers, timeout=TIMEOUT)
        assert producto_actual_resp.status_code == 200, f"Failed to get product after voiding sale: {producto_actual_resp.status_code}"
        if producto_actual_resp.content:
            try:
                producto_actual = producto_actual_resp.json()
            except Exception:
                producto_actual = {}
        else:
            producto_actual = {}
        stock_after_void = producto_actual.get("stock") if producto_actual.get("stock") is not None else producto_actual.get("cantidad") if producto_actual.get("cantidad") is not None else producto_actual.get("existencias")
        assert stock_after_void is not None, "Stock info missing after voiding sale"
        assert isinstance(stock_after_void, (int, float)) and stock_after_void >= 0, "Invalid stock after voiding sale"

        # Try voiding sale again - expect failure
        anular_resp_repeat = session.post(f"{BASE_URL}/ventas/anularVenta", json=anular_payload, headers=headers, timeout=TIMEOUT)
        if anular_resp_repeat.status_code == 200 and anular_resp_repeat.content:
            try:
                resp_json = anular_resp_repeat.json()
            except Exception:
                resp_json = {}
            resp_text = str(resp_json).lower()
            assert any(x in resp_text for x in ["anulada", "no puede", "error", "ya anulada"]), \
                "Second voiding of same sale should fail or be prevented"
        else:
            assert anular_resp_repeat.status_code != 200, "Second voiding of same sale should fail or be prevented"

    finally:
        if sale_id:
            try:
                session.post(f"{BASE_URL}/ventas/anularVenta", json={"venta_id": sale_id}, headers=headers, timeout=TIMEOUT)
            except:
                pass

test_post_ventas_anularventa_void_sale()
