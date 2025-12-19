import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class MeasuresPage extends BasePage {
  constructor(page) {
    super(page);
    this.measuresTable = page.locator('#tblMedidas'); // Asumiendo ID para la tabla
    this.newMeasureButton = page.locator('button[onclick="frmMedida();"]');

    // Locators for the measure modal (create/edit)
    this.modal = page.locator('#myModal');
    this.modalTitle = this.modal.locator('#title');
    this.nombreInput = this.modal.locator('#nombre');
    this.nombreCortoInput = this.modal.locator('#nombre_corto');
    this.saveButton = this.modal.locator('#btnAccion');
  }

  async goto() {
    await this.page.goto('medidas');
    await this.page.waitForLoadState('networkidle');
    await this.page.waitForSelector('table', { timeout: 10000 });
  }

  async createMeasure(name, shortName) {
    await this.newMeasureButton.click();
    await expect(this.modalTitle).toHaveText('Nueva Medida'); // Asegúrate de la capitalización correcta

    await this.nombreInput.fill(name);
    await this.nombreCortoInput.fill(shortName);
    
    await this.saveButton.click();
    await this.page.waitForResponse(resp => resp.url().includes('/medidas/registrar') && resp.status() === 200);
    await this.page.waitForSelector('.swal2-container', { state: 'hidden', timeout: 5000 });
    await this.page.waitForTimeout(500); // Pequeña espera para recarga de tabla
  }

  async isMeasureInTable(name) {
    const measureRow = this.measuresTable.locator('tbody tr').filter({ hasText: name });
    return measureRow.isVisible();
  }
}
