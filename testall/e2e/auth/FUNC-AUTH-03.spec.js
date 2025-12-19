import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { TestUsers } from '../../fixtures/testData';
import { SweetAlertHelper } from '../../helpers/sweetalert.helper';

test.describe('Authentication', () => {
  test('FUNC-AUTH-03: Failed login with incorrect password', async ({ page }) => {
    const loginPage = new LoginPage(page);

    await loginPage.goto();
    await loginPage.loginFailure(TestUsers.admin.email, 'wrongpassword');

    // Assert that the SweetAlert modal shows the correct error message
    const alertMessage = await SweetAlertHelper.getMessage(page);
    await expect(alertMessage).toContain('Usuario o contraseña incorrecta');
  });
});