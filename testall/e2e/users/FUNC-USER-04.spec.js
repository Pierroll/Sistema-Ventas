import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { UsersPage } from '../../pages/UsersPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Users (Admin)', () => {
  let loginPage;
  let dashboardPage;
  let usersPage;
  let uniqueUserName;
  let uniqueUserEmail;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    dashboardPage = new DashboardPage(page);
    usersPage = new UsersPage(page);

    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    await expect(page).toHaveURL(/.*administracion/);

    // Generar un nombre y correo únicos para el usuario de prueba
    const timestamp = Date.now();
    uniqueUserName = `Test User ${timestamp}`;
    uniqueUserEmail = `testuser${timestamp}@example.com`;
  });

  test('FUNC-USER-04: Deactivate (soft delete) a user', async ({ page }) => {
    // 1. Navegar a la página de usuarios desde el dashboard
    await dashboardPage.navigateToUsers();

    // 2. Crear un nuevo usuario para desactivar
    await usersPage.createUser(uniqueUserName, uniqueUserEmail, 'password123', 'GENERAL');

    // Esperar a que el mensaje de éxito aparezca y desaparezca (si aplica)
    // O verificar que el usuario aparece en la tabla de activos
    await usersPage.page.waitForLoadState('networkidle');
    await expect(usersPage.userTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail })).toBeVisible();
    let status = await usersPage.getUserStatus(uniqueUserEmail);
    expect(status).toContain('Activo');

    // 3. Desactivar el usuario
    await usersPage.deactivateUser(uniqueUserEmail);

    // 4. Verificar que el usuario ya no está en la tabla de usuarios activos
    await usersPage.page.waitForLoadState('networkidle');
    await expect(usersPage.userTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail })).not.toBeVisible();

    // 5. Navegar a la página de usuarios inactivos
    await usersPage.gotoInactive();

    // 6. Verificar que el usuario aparece en la tabla de usuarios inactivos con estado 'Inactivo'
    await usersPage.page.waitForLoadState('networkidle');
    await expect(usersPage.userTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail })).toBeVisible();
    status = await usersPage.getUserStatus(uniqueUserEmail);
    expect(status).toContain('Inactivo');
  });
});
