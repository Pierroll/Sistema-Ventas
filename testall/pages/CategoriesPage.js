import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class CategoriesPage extends BasePage {
  constructor(page) {
    super(page);
    this.categoriesTable = page.locator('#tblCategorias'); // Asumiendo ID para la tabla
    this.newCategoryButton = page.locator('button[onclick="frmCategoria();"]');

    // Locators for the category modal (create/edit)
    this.modal = page.locator('#myModal');
    this.modalTitle = this.modal.locator('#title');
    this.nombreInput = this.modal.locator('#nombre');
    this.saveButton = this.modal.locator('#btnAccion');
  }

  async goto() {
    await this.page.goto('categorias');
    await this.page.waitForLoadState('networkidle');
    await this.page.waitForSelector('table', { timeout: 10000 });
  }

  async createCategory(name) {
    await this.newCategoryButton.click();
    await expect(this.modalTitle).toHaveText('Nueva Categoria'); // Asegúrate de la capitalización correcta

    await this.nombreInput.fill(name);
    
    await this.saveButton.click();
    await this.page.waitForResponse(resp => resp.url().includes('/categorias/registrar') && resp.status() === 200);
    await this.page.waitForSelector('.swal2-container', { state: 'hidden', timeout: 5000 });
    await this.page.waitForTimeout(500); // Pequeña espera para recarga de tabla
  }

  async isCategoryInTable(name) {
    const categoryRow = this.categoriesTable.locator('tbody tr').filter({ hasText: name });
    return categoryRow.isVisible();
  }
}