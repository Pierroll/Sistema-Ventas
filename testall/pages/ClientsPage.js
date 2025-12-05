import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class ClientsPage extends BasePage {
  constructor(page) {
    super(page);
    this.searchInput = page.locator('input[type="search"]');
    this.tableRows = page.locator('table tbody tr');
  }

  async goto() {
    // goto es ahora relativo a la baseURL (administracion/)
    await this.page.goto('http://localhost/venta/clientes');
    await this.page.waitForLoadState('networkidle');
  }

  async searchClient(name) {
    await this.searchInput.fill(name);
    await this.page.waitForTimeout(500);
  }

  async getSearchResults() {
    return this.tableRows;
  }
}
