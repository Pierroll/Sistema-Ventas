import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { UsersPage } from '../../pages/UsersPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Users', () => {

  test('FUNC-USER-07: Seller permissions', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const usersPage = new UsersPage(page);

    // 1. Login como vendedor
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.vendedor.email, TestUsers.vendedor.password);
    await dashboardPage.isOnDashboard();

    // 2. Navegar a Usuarios
    await usersPage.goto();
    await page.waitForLoadState('networkidle');

    // 3. Verificar que el vendedor NO puede ver el botón "Nuevo Usuario"
    // El botón no debería existir o no debería ser visible
    await expect(usersPage.newUserButton).not.toBeVisible();
    
    // Alternativa: verificar que no tiene permisos
    // Si el sistema muestra un mensaje de "sin permisos", verificarlo aquí
  });

  test('FUNC-USER-07-ADMIN: Admin can see New User button', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const usersPage = new UsersPage(page);

    // 1. Login como admin
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    await dashboardPage.isOnDashboard();

    // 2. Navegar a Usuarios
    await usersPage.goto();
    await page.waitForLoadState('networkidle');

    // 3. Verificar que el admin SÍ puede ver el botón "Nuevo Usuario"
    await expect(usersPage.newUserButton).toBeVisible();
  });

});