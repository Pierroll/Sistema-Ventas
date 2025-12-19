import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class DashboardPage extends BasePage {
  constructor(page) {
    super(page);
    this.userName = page.locator('.dropdown-title');
    this.userDropdown = page.locator('.dropdown-toggle.nav-link-user');
    this.logoutButton = page.locator('a[href*="salir"].dropdown-item');
    this.usersLink = page.locator('div.card:has(h5:has-text("Usuarios")) a:has-text("Ver Detalle")');
    this.productsLink = page.locator('div.card:has(h5:has-text("Productos")) a:has-text("Ver Detalle")');
    this.clientsLink = page.locator('div.card:has(h5:has-text("Clientes")) a:has-text("Ver Detalle")');
    this.categoriesLink = page.locator('div.card:has(h5:has-text("Categorias")) a:has-text("Ver Detalle")');
    this.measuresLink = page.locator('div.card:has(h5:has-text("Medidas")) a:has-text("Ver Detalle")');
  }

  async navigateToUsers() {
    await this.usersLink.click();
    await this.page.waitForURL(/.*\/usuarios/);
    await this.page.waitForSelector('table', { timeout: 10000 }); // Espera a que la tabla de usuarios esté visible
  }

  async navigateToProducts() {
    await this.productsLink.click();
    await this.page.waitForURL(/.*\/productos\/admin/);
    // FIX: Esperar el selector de tabla correcto y específico.
    await this.page.waitForSelector('#tblProductos', { timeout: 10000 });
  }

  async navigateToClients() {
    await this.clientsLink.click();
    await this.page.waitForURL(/.*\/clientes/);
    await this.page.waitForSelector('table', { timeout: 10000 }); // Espera a que la tabla de clientes esté visible
  }

  async navigateToCategories() {
    await this.categoriesLink.click();
    await this.page.waitForURL(/.*\/categorias/);
    await this.page.waitForSelector('table', { timeout: 10000 });
  }

  async navigateToMeasures() {
    await this.measuresLink.click();
    await this.page.waitForURL(/.*\/medidas/);
    await this.page.waitForSelector('table', { timeout: 10000 });
  }

  async isOnDashboard() {
    await expect(this.page).toHaveURL(/.*\/administracion\/home/);
    await this.page.waitForLoadState('networkidle');
    return this.userName.isVisible();
  }

  async getUserName() {
    // Abrir el dropdown primero para que el nombre sea visible
    await this.userDropdown.click();
    await this.userName.waitFor({ state: 'visible', timeout: 5000 });
    const name = await this.userName.textContent();
    // Cerrar el dropdown
    await this.userDropdown.click();
    return name.trim();
  }

  async logout() {
    // Abrir dropdown
    await this.userDropdown.click();
    // Click en logout
    await this.logoutButton.click();
    // Esperar redirección
    await this.page.waitForURL('http://localhost/venta/', { timeout: 10000 });
  }
}