import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { ProductsPage } from '../../pages/ProductsPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Products Management', () => {

  test('FUNC-PRO-01: Edit selling price of a product', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const productsPage = new ProductsPage(page);

    // --- 1. Iniciar sesión como Admin ---
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    // --- 2. Navegar a la página de productos desde el dashboard ---
    await dashboardPage.navigateToProducts();
    
    // --- 3. Crear un producto único para la prueba ---
    const randomId = Date.now();
    const productCode = `PROD-${randomId}`;
    const productDesc = `Test Product ${randomId}`;
    const purchasePrice = 100.00;
    const initialSellingPrice = 120.00;
    const newSellingPrice = 150.50;
    const unit = 'UNIDAD'; // Asegúrate de que esta unidad exista
    const category = 'ACCESORIOS'; // Asegúrate de que esta categoría exista

    await productsPage.createProduct(productCode, productDesc, purchasePrice, initialSellingPrice, unit, category);
    
    // Verificar que el producto fue creado y su precio inicial es correcto en la tabla
    let currentPrice = await productsPage.getProductSellingPrice(productCode);
    expect(currentPrice).toBe(initialSellingPrice);

    // --- 4. Editar el producto y actualizar el precio de venta ---
    await productsPage.editProduct(productCode);
    await productsPage.updateSellingPrice(newSellingPrice);
    await productsPage.saveModal();

    // --- 5. Verificar que el precio de venta se actualizó correctamente en la tabla ---
    await productsPage.page.waitForLoadState('networkidle');
    const updatedPrice = await productsPage.getProductSellingPrice(productCode);
    expect(updatedPrice).toBe(newSellingPrice);

    console.log(`✅ Producto '${productDesc}' (${productCode}): Precio de venta actualizado de ${initialSellingPrice} a ${newSellingPrice}`);
  });

});