import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class ClientsPage extends BasePage {
  constructor(page) {
    super(page);
    this.searchInput = page.locator('input[type="search"]');
    this.tableRows = page.locator('table tbody tr');
    this.newClientButton = page.locator('button[onclick="frmCliente();"]');

    // Locators for the client modal (create/edit)
    this.modal = page.locator('#myModal');
    this.modalTitle = this.modal.locator('#title');
    this.dniInput = this.modal.locator('#dni');
    this.nombreInput = this.modal.locator('#buscarCliente'); // Este ID es para el nombre en el modal
    this.telefonoInput = this.modal.locator('#telefono');
    this.direccionTextarea = this.modal.locator('#direccion');
    this.saveButton = this.modal.locator('#btnAccion');
  }

  async goto() {
    await this.page.goto('clientes'); // URL relativa
    await this.page.waitForLoadState('networkidle');
    await this.page.waitForSelector('table', { timeout: 10000 });
  }

  async searchClient(name) {
    await this.searchInput.fill(name);
    await this.page.waitForTimeout(500); // Esperar que DataTables filtre
  }

  async getSearchResults() {
    return this.tableRows;
  }

  async createClient(dni, name, phone, address) {
    await this.newClientButton.click();
    await expect(this.modalTitle).toHaveText('Nuevo cliente'); // El título real del modal

    await this.dniInput.fill(dni);
    await this.nombreInput.fill(name);
    await this.telefonoInput.fill(phone);
    await this.direccionTextarea.fill(address);
    
    await this.saveButton.click();
    await this.page.waitForResponse(resp => resp.url().includes('/clientes/registrar') && resp.status() === 200);
    await this.page.waitForSelector('.swal2-container', { state: 'hidden', timeout: 5000 }); // Esperar a que SweetAlert desaparezca
    await this.page.waitForTimeout(500); // Pequeña espera para recarga de tabla
  }

  async isClientInTable(dni, name) {
    const clientRow = this.tableRows.filter({ hasText: dni }).filter({ hasText: name });
    return clientRow.isVisible();
  }
}
