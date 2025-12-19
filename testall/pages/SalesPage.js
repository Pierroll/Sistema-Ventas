import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class SalesPage extends BasePage {
  constructor(page) {
    super(page);
    this.productSearchInput = page.locator('#codigo_producto'); // Asumiendo este ID para el input de búsqueda
    this.cartTable = page.locator('#tblDetalle'); // Asumiendo este ID para la tabla del carrito
    this.totalPagar = page.locator('#total_pagar'); // Asumiendo este ID para el total a pagar
    this.alertas = page.locator('#alertas'); // Asumiendo un div para mensajes de alerta

    // Locators for product search results (autocomplete/dropdown)
    this.autocompleteResult = page.locator('.ui-menu-item-wrapper'); // Selector común para resultados de autocompletado de jQuery UI
  }

  async goto() {
    await this.page.goto('ventas');
    await this.page.waitForLoadState('networkidle');
    await this.page.waitForSelector('#codigo_producto', { timeout: 10000 }); // Espera a que el input de búsqueda esté visible
  }

  async searchProductAndAddToCart(productCode) {
    await this.productSearchInput.fill(productCode);
    // Esperar a que aparezcan los resultados del autocompletado
    await this.autocompleteResult.first().waitFor({ state: 'visible' });
    // Seleccionar el primer resultado (asumiendo que es el producto correcto)
    await this.autocompleteResult.first().click();

    // Esperar una posible alerta de éxito o la actualización del carrito
    await this.page.waitForResponse(resp => resp.url().includes('/ventas/agregarVenta') && resp.status() === 200);
    await this.page.waitForSelector('.swal2-container', { state: 'hidden', timeout: 5000 }); // Esperar a que SweetAlert desaparezca
  }

  async isProductInCart(productCode) {
    // Buscar el producto por su código en la tabla del carrito
    return this.cartTable.locator('tbody tr').filter({ hasText: productCode }).isVisible();
  }

  async getProductCartQuantity(productCode) {
    const productRow = this.cartTable.locator('tbody tr').filter({ hasText: productCode });
    // Asumiendo que la columna de cantidad es la segunda (índice 1)
    const quantityInput = productRow.locator('input[type="number"]'); // O un td si es solo texto
    const quantity = await quantityInput.inputValue(); // O .textContent() si no es input
    return parseInt(quantity, 10);
  }

  async getCartTotal() {
    const totalText = await this.totalPagar.textContent();
    // Limpiar el texto para obtener solo el número (ej. "150.00" de "S/. 150.00")
    return parseFloat(totalText.replace(/[^0-9.]/g, ''));
  }
}