import { test, expect } from '@playwright/test';  // ✅ Sin espacio
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { UsersPage } from '../../pages/UsersPage';
import { TestUsers } from '../../fixtures/testData';
import { SweetAlertHelper } from '../../helpers/sweetalert.helper';

test.describe('Users (Admin)', () => {
  let loginPage;
  let dashboardPage;
  let usersPage;
  let page; // Declarar 'page' aquí para que esté disponible en el beforeAll y en el test

  test.beforeEach(async ({ browser }) => {
    page = await browser.newPage(); // Asignar 'page'
    loginPage = new LoginPage(page);
    dashboardPage = new DashboardPage(page);
    usersPage = new UsersPage(page);

    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    await dashboardPage.navigateToUsers();
    await usersPage.isOnUsersPage();
  });

  test('FUNC-USER-04: Deactivate (soft delete) a user', async () => {
    // 1. Crear un usuario único para esta prueba
    const timestamp = Date.now();
    const uniqueUserName = `Test User ${timestamp}`;
    const uniqueUserEmail = `testuser${timestamp}@example.com`;
    await usersPage.createUser(uniqueUserName, uniqueUserEmail, 'password', 'GENERAL'); // Caja 'GENERAL'

    // Esperar a que el modal de éxito de registro aparezca
    await SweetAlertHelper.waitForAlert(page);
    
    // FIX: Este modal es AUTO-DISMISSIBLE (se cierra solo, no tiene botón visible)
    // Solo verificamos que apareció y esperamos a que desaparezca
    const registerSuccessTitle = await SweetAlertHelper.getTitle(page);
    expect(registerSuccessTitle.toUpperCase()).toContain('USUARIO REGISTRADO');
    
    // NO hacemos click - el modal se cierra automáticamente
    await SweetAlertHelper.waitForAlertToDisappear(page);

    // 2. Verificar que el usuario recién creado está en la lista de activos
    await usersPage.page.waitForLoadState('networkidle');
    await expect(usersPage.userTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail })).toBeVisible();
    let status = await usersPage.getUserStatus(uniqueUserEmail);
    expect(status).toContain('Activo');

    // 3. Desactivar el usuario
    await usersPage.deactivateUser(uniqueUserEmail);
    
    // Esperar y aceptar el diálogo de confirmación de eliminación
    await SweetAlertHelper.waitForAlert(page);
    const confirmMessage = await SweetAlertHelper.getMessage(page);
    expect(confirmMessage).toContain('El registro no se eliminará de forma permanente');
    await SweetAlertHelper.clickConfirm(page);
    await SweetAlertHelper.waitForAlertToDisappear(page);

    // NO hay modal de éxito después de desactivar - la acción se completa directamente
    
    // 5. Verificar que el usuario ya no está en la tabla de activos
    await usersPage.page.waitForLoadState('networkidle');
    await expect(usersPage.userTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail })).not.toBeVisible();
    
    // 6. Navegar a la página de usuarios inactivos
    await usersPage.gotoInactive();

    // 7. Verificar que el usuario aparece en la tabla de usuarios inactivos con estado 'Inactivo'
    await usersPage.page.waitForLoadState('networkidle');
    const inactiveTable = page.locator('#tbl'); // Usar el ID correcto para la tabla de inactivos
    await expect(inactiveTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail })).toBeVisible();
    
    const userRow = inactiveTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail });
    const statusCell = userRow.locator('td').nth(4);
    status = await statusCell.textContent();
    expect(status).toContain('Inactivo');

    // OPCIONAL: Reingresar el usuario para limpiar el estado de la base de datos
    const reingresarButton = inactiveTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail }).locator('button[onclick*="btnReingresarUser"]');
    await reingresarButton.click();
    await SweetAlertHelper.waitForAlert(page);
    
    // El texto está en el TÍTULO, no en el mensaje
    const reingresarConfirmTitle = await SweetAlertHelper.getTitle(page);
    expect(reingresarConfirmTitle).toContain('Esta seguro de restaurar');
    
    await SweetAlertHelper.clickConfirm(page);
    await SweetAlertHelper.waitForAlertToDisappear(page);

    // NO hay modal de éxito después de reingresar - similar a la desactivación
    
    // Verificar que el usuario aparece de nuevo en la tabla de activos (opcional)
    await usersPage.goto(); // Volver a la página de usuarios activos
    await expect(usersPage.userTable.locator(`tbody tr`).filter({ hasText: uniqueUserEmail })).toBeVisible();
    status = await usersPage.getUserStatus(uniqueUserEmail);
    expect(status).toContain('Activo');
  });
});
