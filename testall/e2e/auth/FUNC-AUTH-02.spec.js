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
    
    // 1. Assert that the redirect goes to the admin dashboard (reflecting current app bug)
    await expect(page).toHaveURL(/.*\/administracion\/home/);

    // 2. Assert that the user's name is visible in the dropdown
    const userName = await dashboardPage.getUserName();
    expect(userName).toBe('VENDEDOR 01');
  });
});
