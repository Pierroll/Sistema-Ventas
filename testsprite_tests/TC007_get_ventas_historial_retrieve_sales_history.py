import requests
from requests.auth import HTTPBasicAuth
from datetime import datetime, timedelta

BASE_URL = "http://localhost/venta"
USERNAME = "tester"
PASSWORD = "Passw0rd!"
TIMEOUT = 30

def test_get_ventas_historial_retrieve_sales_history():
    auth = HTTPBasicAuth(USERNAME, PASSWORD)

    # Step 1: Verify server responds with 200 at a basic endpoint (e.g., GET /ventas to check sales endpoint basic availability)
    try:
        response_status = requests.get(f"{BASE_URL}/ventas", auth=auth, timeout=TIMEOUT)
        assert response_status.status_code == 200
    except requests.RequestException as e:
        assert False, f"Server unreachable or error: {e}"

    # Prepare filter parameters for historial endpoint, filtering by date range and optionally by customer id and other params
    filter_params = {
        # For testing, select a date range: last 30 days
        "fecha_inicio": (datetime.now() - timedelta(days=30)).strftime("%Y-%m-%d"),
        "fecha_fin": datetime.now().strftime("%Y-%m-%d"),
        # Assume the API allows filtering by 'cliente' parameter (example customer id)
    }

    # Attempt 1: Get sales history filtered by date range only
    try:
        response = requests.get(f"{BASE_URL}/ventas/historial", params=filter_params, auth=auth, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected 200 OK but got {response.status_code}"
        content = response.content.strip()
        assert content, "Response content is empty"
        data = response.json()
        # Expect data is a list or dict containing sales records
        assert isinstance(data, (list, dict)), "Response data is neither list nor dict"
        # If list, check if records have expected keys
        records = data if isinstance(data, list) else data.get('ventas', [])
        for rec in records:
            # Validate minimal expected keys in each record
            assert 'id_venta' in rec, "Missing sale id in record"
            assert 'fecha' in rec, "Missing date in record"
            assert 'cliente' in rec, "Missing client info in record"
        # Check all records dates within the filter
        for rec in records:
            # Extract sale date
            sale_date_str = rec.get('fecha')
            assert sale_date_str is not None, "Sale date missing in record"
            sale_date = datetime.strptime(sale_date_str[:10], "%Y-%m-%d")
            start_date = datetime.strptime(filter_params['fecha_inicio'], "%Y-%m-%d")
            end_date = datetime.strptime(filter_params['fecha_fin'], "%Y-%m-%d")
            assert start_date <= sale_date <= end_date, "Sale date out of filter range"
    except requests.RequestException as e:
        assert False, f"Request to /ventas/historial failed: {e}"
    except (ValueError, AssertionError) as e:
        assert False, f"Response validation failed: {e}"

    # Attempt 2: Get sales history filtered by date and a specific (fake) customer id = 1 to test filtering
    filter_params_customer = filter_params.copy()
    filter_params_customer["cliente"] = "1"

    try:
        response_cust = requests.get(f"{BASE_URL}/ventas/historial", params=filter_params_customer, auth=auth, timeout=TIMEOUT)
        assert response_cust.status_code == 200, f"Expected 200 OK but got {response_cust.status_code}"
        content_cust = response_cust.content.strip()
        assert content_cust, "Response content is empty"
        data_cust = response_cust.json()
        assert isinstance(data_cust, (list, dict)), "Response data is neither list nor dict"
        records_cust = data_cust if isinstance(data_cust, list) else data_cust.get('ventas', [])
        for rec in records_cust:
            # Validate client id matches filter (depending on response schema)
            cliente_id = rec.get('cliente')
            # cliente_id might be int or str, ensure comparison works
            if cliente_id is not None:
                assert str(cliente_id) == "1", f"Record client id {cliente_id} does not match filter 1"
    except requests.RequestException as e:
        assert False, f"Request to /ventas/historial with customer filter failed: {e}"
    except (ValueError, AssertionError) as e:
        assert False, f"Response validation with customer filter failed: {e}"

test_get_ventas_historial_retrieve_sales_history()