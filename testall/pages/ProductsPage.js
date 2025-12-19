import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class ProductsPage extends BasePage {
  constructor(page) {
    super(page);
    this.productTable = page.locator('#tblProductos');
    this.newProductButton = page.locator('button[onclick="frmProducto();"]');
    // Selector para el campo de búsqueda de la tabla de productos
    this.searchInput = page.locator('input[aria-controls="tblProductos"]');

    // Locators for the product modal (create/edit)
    this.modal = page.locator('#myModal');
    this.modalTitle = this.modal.locator('#title');
    this.codigoInput = this.modal.locator('#codigo');
    this.descripcionInput = this.modal.locator('#descripcion');
    this.precioCompraInput = this.modal.locator('#precio_compra');
    this.precioVentaInput = this.modal.locator('#precio_venta');
    this.medidaSelect = this.modal.locator('#medida');
    this.categoriaSelect = this.modal.locator('#categoria');
    this.saveButton = this.modal.locator('#btnAccion');
    this.fotoActualInput = this.modal.locator('#foto_actual'); // hidden input for current photo
    this.idInput = this.modal.locator('#id'); // hidden input for product ID
  }

  async goto() {
    await this.page.goto('productos/admin');
    await this.page.waitForLoadState('networkidle');
    await this.page.waitForSelector('#tblProductos', { timeout: 10000 });
  }

  async createProduct(code, description, purchasePrice, sellingPrice, unit, category) {
    await this.newProductButton.click();
    await expect(this.modalTitle).toHaveText('NUEVO PRODUCTO');

    await this.codigoInput.fill(code);
    await this.descripcionInput.fill(description);
    await this.precioCompraInput.fill(purchasePrice.toString());
    await this.precioVentaInput.fill(sellingPrice.toString());
    await this.medidaSelect.selectOption({ label: unit });
    await this.categoriaSelect.selectOption({ label: category });
    
    await this.saveButton.click();
    // Esperar respuesta de registro y recarga de tabla
    await this.page.waitForResponse(resp => resp.url().includes('/productos/registrar') && resp.status() === 200);
    await this.page.waitForTimeout(500); // Pequeña espera para que la tabla se repinte
  }

  async editProduct(productCode) {
    // FIX: Usar el buscador para encontrar el producto y asegurar que la fila es visible
    await this.searchInput.fill(productCode);
    // Esperar a que la tabla se filtre y solo una fila (la del producto) sea visible
    await expect(this.productTable.locator('tbody tr')).toHaveCount(1, { timeout: 5000 });

    const productRow = this.productTable.locator('tbody tr').first(); // Ahora que solo hay una, podemos tomar la primera
    const editButton = productRow.locator('button[onclick*="btnEditarPro"]');
    
    // Ahora que la fila es visible, el scroll y el clic deberían funcionar
    await editButton.scrollIntoViewIfNeeded();

    // DEBUG: Resaltar el botón antes de hacer clic para verificar el selector en el video
    await editButton.highlight();

    await editButton.click();
    await expect(this.modalTitle).toHaveText('Actualizar Producto');
    await this.modal.waitFor({ state: 'visible' });
    // Verificar que el ID del producto está cargado en el campo oculto
    await expect(this.idInput).not.toHaveValue(''); 
  }

  async updateSellingPrice(newPrice) {
    await this.precioVentaInput.fill(newPrice.toString());
  }

  async saveModal() {
    await this.saveButton.click();
    // Esperar respuesta de modificación y recarga de tabla
    await this.page.waitForResponse(resp => resp.url().includes('/productos/registrar') && resp.status() === 200);
    await this.page.waitForTimeout(500); // Pequeña espera para que la tabla se repinte
  }

  async getProductSellingPrice(productCode) {
    // Usar el buscador para asegurar que la fila es visible
    await this.searchInput.fill(productCode);
    await expect(this.productTable.locator('tbody tr')).toHaveCount(1, { timeout: 5000 });
    
    const productRow = this.productTable.locator('tbody tr').first();
    // Asumiendo que el precio de venta es la 5ta columna (índice 4 si contamos desde 0)
    // Código, Descripción, Precio Compra, Precio Venta, Stock, Medida, Categoría, Estado, Acciones
    // Columna 0     1             2             3          4      5       6          7       8
    const priceCell = productRow.locator('td').nth(3); // Indice 3 para precio de venta si es la 4ta columna visible
    const priceText = await priceCell.textContent();
    // Limpiar el texto para obtener solo el número (ej. "150.00" de "S/. 150.00")
    return parseFloat(priceText.replace(/[^0-9.]/g, ''));
  }
}