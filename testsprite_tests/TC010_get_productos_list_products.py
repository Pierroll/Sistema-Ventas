import requests

BASE_URL = "http://localhost/venta"
TIMEOUT = 30

def test_get_product_list():
    url = f"{BASE_URL}/productos"
    try:
        response = requests.get(url, timeout=TIMEOUT)
    except requests.RequestException as e:
        assert False, f"Request to get productos failed: {e}"

    assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"

    try:
        products = response.json()
    except ValueError:
        assert False, "Response body is not valid JSON"

    assert isinstance(products, list), "Products response should be a list"

    for product in products:
        assert isinstance(product, dict), "Each product should be a dictionary"
        assert "id" in product, "Product missing 'id'"
        assert "nombre" in product, "Product missing 'nombre'"
        assert "stock" in product, "Product missing 'stock'"
        assert isinstance(product["stock"], (int, float)), "'stock' should be numeric"
        assert "categoria" in product, "Product missing 'categoria'"
        assert "unidad_medida" in product, "Product missing 'unidad_medida'"

test_get_product_list()