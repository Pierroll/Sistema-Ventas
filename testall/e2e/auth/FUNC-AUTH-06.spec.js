import { test, expect } from '@playwright/test';
import { DashboardPage } from '../../pages/DashboardPage';
import { LoginPage } from '../../pages/LoginPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Authentication', () => {
  test('FUNC-AUTH-06: Successful logout', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);

    // 1. Login
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);

    // Asegurarnos de que el dashboard está completamente cargado
    await dashboardPage.isOnDashboard();
    
    // 2. Logout
    await dashboardPage.logout();

    // 3. Verificar que estamos de vuelta en la página de login
    await expect(loginPage.loginButton).toBeVisible();
  });
});
