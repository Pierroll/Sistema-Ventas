import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Authentication', () => {
  test('FUNC-AUTH-01: Successful login as admin', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);

    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    // La aserción es simple: ¿el nombre de usuario es visible en el dashboard?
    const userNameVisible = await dashboardPage.getUserName();
    expect(userNameVisible).toBeTruthy();
  });
});
