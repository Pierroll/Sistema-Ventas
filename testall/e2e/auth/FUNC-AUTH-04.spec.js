import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { SweetAlertHelper } from '../../helpers/sweetalert.helper';

test.describe('Authentication', () => {
  test('FUNC-AUTH-04: Failed login with non-existent email', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();

    // Intentar login con un email que no existe
    await loginPage.loginFailure('nonexistent@user.com', 'anypassword');

    // Validar que se muestra el mensaje de error esperado en el modal
    const alertMessage = await SweetAlertHelper.getMessage(page);
    await expect(alertMessage).toContain('Usuario o contraseña incorrecta');
  });
});
