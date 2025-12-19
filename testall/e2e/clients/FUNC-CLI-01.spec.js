import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { ClientsPage } from '../../pages/ClientsPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Clients Management', () => {

  test('FUNC-CLI-01: Create a new client', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const clientsPage = new ClientsPage(page);

    // --- 1. Iniciar sesión como Admin ---
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    // --- 2. Navegar a la página de clientes desde el dashboard ---
    await dashboardPage.navigateToClients();
    
    // --- 3. Generar datos únicos para el nuevo cliente ---
    const randomId = Date.now();
    const clientDni = `100${randomId.toString().substring(0, 8)}`; // DNI de 11 dígitos para evitar colisiones
    const clientName = `Cliente Nuevo ${randomId}`;
    const clientPhone = `9${randomId.toString().substring(0, 8)}`; // Teléfono de 9 dígitos
    const clientAddress = `Dirección del Cliente ${randomId}`;

    // --- 4. Crear el cliente ---
    await clientsPage.createClient(clientDni, clientName, clientPhone, clientAddress);
    
    // --- 5. Verificar que el nuevo cliente aparece en la tabla ---
    const isClientVisible = await clientsPage.isClientInTable(clientDni, clientName);
    expect(isClientVisible, `El cliente '${clientName}' con DNI '${clientDni}' debe ser visible en la tabla`).toBe(true);

    console.log(`✅ Cliente '${clientName}' con DNI '${clientDni}' creado y verificado exitosamente.`);
  });

});
