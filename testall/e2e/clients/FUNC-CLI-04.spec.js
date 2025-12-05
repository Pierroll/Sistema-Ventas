import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { ClientsPage } from '../../pages/ClientsPage';
import { TestUsers, TestClients } from '../../fixtures/testData';

test.describe('Clients', () => {
  test('FUNC-CLI-04: Search for a client', async ({ page }) => {
    // --- PREPARACIÓN DE DATOS ---
    const setupResponse = await page.request.get('http://localhost/venta/test_setup.php');
    expect(setupResponse.ok()).toBeTruthy();
    // --------------------------

    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const clientsPage = new ClientsPage(page);

    // 1. Login
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    await dashboardPage.isOnDashboard();

    // 2. Navegar a Clientes
    await clientsPage.goto();

    // 3. Buscar
    await clientsPage.searchClient(TestClients.searchTerm);

    // 4. Verificar resultados
    const firstRow = clientsPage.tableRows.first();
    await expect(firstRow).toContainText(TestClients.searchTerm);
  });
});
