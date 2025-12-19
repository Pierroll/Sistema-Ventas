import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { SalesPage } from '../../pages/SalesPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Sales Management', () => {

  test('FUNC-VENTA-01: Add product to cart (sufficient stock)', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const salesPage = new SalesPage(page);

    // --- 1. Iniciar sesión como Admin ---
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    // --- 2. Navegar a la página de ventas ---
    await salesPage.goto();
    
    // --- 3. Definir el producto a añadir y buscarlo ---
    // Usaremos el producto existente que tiene stock suficiente.
    const productCode = '0987654321';
    
    await salesPage.searchProductAndAddToCart(productCode);

    // --- 4. Verificar que el producto fue agregado al carrito ---
    const isProductVisibleInCart = await salesPage.isProductInCart(productCode);
    expect(isProductVisibleInCart, `El producto con código '${productCode}' debe ser visible en el carrito`).toBe(true);

    // --- 5. Verificaciones adicionales (opcional pero recomendado) ---
    // Verificar que la cantidad en el carrito es 1
    const quantityInCart = await salesPage.getProductCartQuantity(productCode);
    expect(quantityInCart, 'La cantidad inicial del producto en el carrito debe ser 1').toBe(1);

    // Verificar que el total a pagar es mayor que cero
    const cartTotal = await salesPage.getCartTotal();
    expect(cartTotal, 'El total a pagar debe ser mayor que 0').toBeGreaterThan(0);

    console.log(`✅ Producto '${productCode}' agregado al carrito exitosamente.`);
  });

});
