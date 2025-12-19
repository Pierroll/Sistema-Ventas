import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { ProductsPage } from '../../pages/ProductsPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Products Management', () => {

  test('FUNC-PRO-01: Edit selling price of an existing product', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const productsPage = new ProductsPage(page);

    // --- 1. Iniciar sesión como Admin ---
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    // --- 2. Navegar a la página de productos desde el dashboard ---
    await dashboardPage.navigateToProducts();
    
    // --- 3. Definir el producto existente a editar ---
    // Usaremos el producto de ejemplo que ya tienes en tu sistema.
    // Asegúrate de que este producto (código y descripción) exista en tu base de datos.
    const productCode = '0987654321';
    const productDesc = 'CARTON'; // Usado para asegurar que filtramos bien, no se usa en el método
    const newSellingPrice = 150.50; // Nuevo precio a establecer

    // Opcional: Obtener el precio actual antes de editar (si es necesario verificar que cambia)
    // Para simplificar, nos enfocaremos en establecer y verificar el nuevo precio.

    // --- 4. Editar el producto y actualizar el precio de venta ---
    await productsPage.editProduct(productCode);
    await productsPage.updateSellingPrice(newSellingPrice);
    await productsPage.saveModal();

    // --- 5. Verificar que el precio de venta se actualizó correctamente en la tabla ---
    await productsPage.page.waitForLoadState('networkidle');
    const updatedPrice = await productsPage.getProductSellingPrice(productCode);
    expect(updatedPrice).toBe(newSellingPrice);

    console.log(`✅ Producto '${productCode}': Precio de venta actualizado a ${newSellingPrice}`);
    // Opcional: Puedes revertir el precio o dejarlo así para la siguiente ejecución.
    // Para una prueba de automatización ideal, se recomienda limpiar el estado después.
    // Pero por simplicidad y siguiendo tu solicitud, lo dejaremos modificado.
  });

});