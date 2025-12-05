export class SweetAlertHelper {
  /**
   * Gets the title of the currently visible SweetAlert.
   * @param {import('@playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<string>} - The title of the SweetAlert.
   */
  static async getTitle(page) {
    return page.locator('#swal2-title').textContent();
  }

  /**
   * Gets the message of the currently visible SweetAlert.
   * @param {import('@playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<string>} - The message of the SweetAlert.
   */
  static async getMessage(page) {
    return page.locator('#swal2-html-container').textContent();
  }

  /**
   * Checks if the SweetAlert is an error alert.
   * @param {import('@playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<boolean>} - True if the alert is an error alert, false otherwise.
   */
  static async isErrorAlert(page) {
    return page.locator('.swal2-icon-error').isVisible();
  }

  /**
   * Checks if the SweetAlert is a success alert.
   * @param {import('@playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<boolean>} - True if the alert is a success alert, false otherwise.
   */
  static async isSuccessAlert(page) {
    return page.locator('.swal2-icon-success').isVisible();
  }

  /**
   * Clicks the confirm button on the SweetAlert.
   * @param {import('@playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<void>}
   */
  static async clickConfirm(page) {
    await page.locator('.swal2-confirm').click();
  }
}
