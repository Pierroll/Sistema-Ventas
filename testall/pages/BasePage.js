import { expect } from '@playwright/test';

export class BasePage {
  /**
   * @param {import('@playwright/test').Page} page
   */
  constructor(page) {
    this.page = page;
  }

  async waitForAjaxResponse(urlPattern) {
    return this.page.waitForResponse(urlPattern);
  }

  async getSweetAlertTitle() {
    return this.page.locator('#swal2-title').textContent();
  }

  async getSweetAlertText() {
    return this.page.locator('#swal2-html-container').textContent();
  }

  async isSweetAlertVisible() {
    return this.page.locator('#swal2-title').isVisible();
  }

  async closeSweetAlert() {
    await this.page.locator('.swal2-confirm').click();
  }
}
