import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class UsersPage extends BasePage {
  constructor(page) {
    super(page);
    this.newUserButton = page.locator('button[onclick="frmUsuario();"]');
    this.tableRows = page.locator('table tbody tr');
  }

  async goto() {
    // CORREGIDO: La URL correcta es /venta/usuarios
    await this.page.goto('http://localhost/venta/usuarios');
    await this.page.waitForLoadState('networkidle');
    
    // Esperar a que cargue la tabla de usuarios
    await this.page.waitForSelector('table', { timeout: 10000 });
  }

  async isOnUsersPage() {
    return this.page.url().includes('/usuarios');
  }

  // Locators for new user modal and actions
  get inactiveUsersLink() { return this.page.locator('a[href*="usuarios/inactivos"]'); }
  get userTable() { return this.page.locator('#tblUsuarios'); }

  get modal() { return this.page.locator('#nuevo_usuario'); }
  get modalTitle() { return this.modal.locator('#title'); }
  get nameInput() { return this.modal.locator('#nombre'); }
  get emailInput() { return this.modal.locator('#correo'); }
  get passwordInput() { return this.modal.locator('#clave'); }
  get confirmPasswordInput() { return this.modal.locator('#confirmar'); }
  get cajaSelect() { return this.modal.locator('#caja'); }
  get saveButton() { return this.modal.locator('#btnAccion'); }

  async findUserRow(email) {
    return this.userTable.locator(`tbody tr`).filter({ hasText: email });
  }

  async gotoInactive() {
    await this.inactiveUsersLink.click();
    await expect(this.page).toHaveURL(/.*usuarios\/inactivos/);
  }

  async openNewUserModal() {
    await this.newUserButton.click(); // Use existing newUserButton
    await expect(this.modalTitle).toHaveText('Nuevo Usuario');
  }

  async createUser(name, email, password, caja) {
    await this.openNewUserModal();
    await this.nameInput.fill(name);
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    await this.confirmPasswordInput.fill(password);
    await this.cajaSelect.selectOption({ label: caja });
    await this.saveButton.click();
    await this.page.waitForResponse(resp => resp.url().includes('/usuarios/registrar') && resp.status() === 200);
  }

  async deactivateUser(email) {
    const userRow = await this.findUserRow(email);
    const deleteButton = userRow.locator('button[onclick*="btnEliminarUser"]');
    
    // DEBUG: Resaltar el botón antes de hacer clic para verificar el selector en el video
    await deleteButton.highlight();

    await deleteButton.click();

    // Esperar y aceptar el diálogo de confirmación (SweetAlert)
    await this.page.locator('.swal2-confirm').click();

    // Esperar la respuesta de la API y la recarga de la tabla
    await this.page.waitForResponse(resp => resp.url().includes('/usuarios/eliminar') && resp.status() === 200);
    await this.page.waitForTimeout(500); // Pequeña espera para que la tabla se repinte
  }

  async getUserStatus(email) {
    const userRow = this.userTable.locator(`tbody tr`).filter({ hasText: email });
    const status = userRow.locator('td').nth(4); // La 5ta columna es el estado
    return status.textContent();
  }
}