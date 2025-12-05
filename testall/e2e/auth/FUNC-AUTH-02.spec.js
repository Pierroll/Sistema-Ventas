import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Authentication', () => {
  test('FUNC-AUTH-02: Successful login as seller', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);

    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.vendedor.email, TestUsers.vendedor.password);
    
    // La aserción es simple: ¿el nombre de usuario es visible en el dashboard?
    const userNameVisible = await dashboardPage.getUserName();
    expect(userNameVisible).toBeTruthy();
  });
});
