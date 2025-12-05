import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';

test.describe('Authentication', () => {
  test('FUNC-AUTH-04: Failed login with non-existent email', async ({ page }) => {
    const loginPage = new LoginPage(page);

    await loginPage.goto();
    await loginPage.loginFailure('no-existe@test.com', 'cualquier-clave');

    // Validar que se muestra el mensaje de error esperado
    await expect(loginPage.alert).toContainText('Usuario o contraseña incorrecta');
  });
});
