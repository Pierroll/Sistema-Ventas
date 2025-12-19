import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('DEBUG: Route-Checking', () => {

  test('Check /usuarios route for Admin', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);

    console.log('\nNavigating to /usuarios as ADMIN...');
    await page.goto('http://localhost/venta/usuarios');
    await page.waitForLoadState('networkidle');

    const finalURL = page.url();
    console.log(`✅ ADMIN Final URL: ${finalURL}`);
    await page.screenshot({ path: 'debug-admin-usuarios-route.png' });
    
    // Verificación simple
    await expect(page).toHaveURL(/.*\/usuarios/);
  });

  test('Check /usuarios route for Seller', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.vendedor.email, TestUsers.vendedor.password);

    console.log('\nNavigating to /usuarios as SELLER...');
    await page.goto('http://localhost/venta/usuarios');
    await page.waitForLoadState('networkidle');

    const finalURL = page.url();
    console.log(`✅ SELLER Final URL: ${finalURL}`);
    await page.screenshot({ path: 'debug-seller-usuarios-route.png' });

    // Verificación de que el vendedor SÍ llega a la página de usuarios
    await expect(page).toHaveURL(/.*\/usuarios/);
  });

});
