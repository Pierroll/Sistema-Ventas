export class AjaxHelper {
  /**
   * Waits for a specific AJAX request and returns its JSON response.
   * @param {import('@playwright/test').Page} page - The Playwright page object.
   * @param {string} urlPattern - The URL pattern to wait for.
   * @returns {Promise<any>} - The JSON response from the AJAX call.
   */
  static async waitForAjaxAndGetJson(page, urlPattern) {
    const response = await page.waitForResponse(urlPattern);
    return response.json();
  }

  /**
   * Waits for multiple AJAX requests to complete.
   * @param {import('@playwright/test').Page} page - The Playwright page object.
   * @param {string[]} urlPatterns - An array of URL patterns to wait for.
   * @returns {Promise<void>} - A promise that resolves when all requests have completed.
   */
  static async waitForMultipleAjax(page, urlPatterns) {
    await Promise.all(
      urlPatterns.map(pattern => page.waitForResponse(pattern))
    );
  }
}
