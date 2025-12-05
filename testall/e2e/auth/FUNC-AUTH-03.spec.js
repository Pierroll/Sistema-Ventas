import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Authentication', () => {
  test('FUNC-AUTH-03: Failed login with incorrect password', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();

    await loginPage.loginFailure(TestUsers.admin.email, 'wrongpassword');

    await expect(loginPage.alert).toContainText('Usuario o contraseña incorrecta');
  });
});