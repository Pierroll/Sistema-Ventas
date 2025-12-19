import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH = HTTPBasicAuth("tester", "Passw0rd!")
TIMEOUT = 30

def test_get_ventas_generarpdf_generate_sales_document():
    try:
        # Step 1: Verify the server is responding with 200 on a basic GET /ventas
        ventas_url = f"{BASE_URL}/ventas"
        r = requests.get(ventas_url, auth=AUTH, timeout=TIMEOUT)
        assert r.status_code == 200, f"Server not responding with 200 on ventas GET, got {r.status_code}"
        # Only parse JSON if content is available
        if r.headers.get('Content-Type', '').startswith('application/json') and r.text.strip():
            ventas_list = r.json()
        else:
            ventas_list = []
        # ventas_list should be a list of sales
        assert isinstance(ventas_list, list), "ventas GET did not return a list"

        # If no sales, create one to generate PDF and test
        sale_id = None
        if not ventas_list:
            # Create minimal product first to add to sale
            # GET products list to receive a product ID
            products_url = f"{BASE_URL}/productos"
            r_products = requests.get(products_url, auth=AUTH, timeout=TIMEOUT)
            assert r_products.status_code == 200, f"Failed to list products, status {r_products.status_code}"
            if r_products.headers.get('Content-Type', '').startswith('application/json') and r_products.text.strip():
                products = r_products.json()
            else:
                products = []
            assert isinstance(products, list) and products, "No products found to create sale"
            product = products[0]
            product_id = product.get("id") or product.get("producto_id") or product.get("id_producto")
            assert product_id is not None, "Product ID not found in product data"

            # Step 2a: Add new sale (agregarVenta)
            agregarventa_url = f"{BASE_URL}/ventas/agregarVenta"
            agregarventa_payload = {
                "id_producto": product_id,
                "cantidad": 1
            }
            r_agregar = requests.post(agregarventa_url, auth=AUTH, json=agregarventa_payload, timeout=TIMEOUT)
            assert r_agregar.status_code == 200, f"Failed agregarVenta, status {r_agregar.status_code}"
            agregar_resp = r_agregar.json()
            # Response should include sale id or sale detail id
            sale_id = agregar_resp.get("id_venta") or agregar_resp.get("idVenta") or agregar_resp.get("venta_id")
            assert sale_id is not None, "No sale ID returned on agregarVenta"

            # Step 2b: Finalize sale (registrarVenta)
            registrarventa_url = f"{BASE_URL}/ventas/registrarVenta"
            registrarventa_payload = {
                "id_venta": sale_id,
                "monto": product.get("precio_venta") or product.get("precio") or 0,
                "cliente_id": None,
                "metodo_pago": "efectivo"
            }
            r_registrar = requests.post(registrarventa_url, auth=AUTH, json=registrarventa_payload, timeout=TIMEOUT)
            assert r_registrar.status_code == 200, f"Failed registrarVenta, status {r_registrar.status_code}"

        else:
            # Use first sale id available
            first_sale = ventas_list[0]
            sale_id = first_sale.get("id") or first_sale.get("id_venta") or first_sale.get("venta_id")
            assert sale_id is not None, "No sale ID found in ventas list"

        # Step 3: Request PDF generation for the sale
        generarpdf_url = f"{BASE_URL}/ventas/generarPdf"
        params = {"id_venta": sale_id}
        r_pdf = requests.get(generarpdf_url, auth=AUTH, params=params, timeout=TIMEOUT, stream=True)
        assert r_pdf.status_code == 200, f"Failed to generate PDF for sale {sale_id}, status {r_pdf.status_code}"

        content_type = r_pdf.headers.get("Content-Type", "")
        assert content_type in ("application/pdf", "application/octet-stream"), f"Unexpected content type for PDF: {content_type}"

        content_disp = r_pdf.headers.get("Content-Disposition", "")
        assert ("filename" in content_disp and (content_disp.endswith('.pdf"') or '.pdf' in content_disp)), \
            "Content-Disposition header does not indicate a PDF filename"

        pdf_content = r_pdf.content
        assert pdf_content and len(pdf_content) > 1000, "PDF content is too small or empty"

    finally:
        # Cleanup: if created a sale, try to cancel/delete it
        if 'sale_id' in locals() and sale_id:
            try:
                delete_url = f"{BASE_URL}/ventas/deleteVenta"
                payload = {"id_venta": sale_id}
                r_del = requests.delete(delete_url, auth=AUTH, json=payload, timeout=TIMEOUT)
                assert r_del.status_code in (200, 204), f"Failed to cleanup sale {sale_id}, status {r_del.status_code}"
            except Exception:
                pass

test_get_ventas_generarpdf_generate_sales_document()
