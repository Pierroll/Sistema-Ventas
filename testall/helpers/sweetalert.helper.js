import { expect } from '@playwright/test';

export class SweetAlertHelper {
  /**
   * Gets the title of the currently visible SweetAlert.
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<string>} - The title of the SweetAlert.
   */
  static async getTitle(page) {
    return page.locator('#swal2-title').textContent();
  }

  /**
   * Gets the message of the currently visible SweetAlert.
   * This method is now more robust to handle different types of content.
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<string>} - The message of the SweetAlert.
   */
  static async getMessage(page) {
    // Wait for the popup to be visible first
    const popup = page.locator('.swal2-popup.swal2-show');
    await popup.waitFor({
      state: 'visible',
      timeout: 5000
    });

    // STRATEGY: Get ALL text from the popup, excluding title
    // Some SweetAlert implementations don't use standard containers
    
    // Try to get text from common containers first
    const containers = [
      '.swal2-html-container',
      '#swal2-html-container',
      '#swal2-content',
      '.swal2-content'
    ];

    for (const selector of containers) {
      try {
        const element = popup.locator(selector);
        const text = await element.textContent({ timeout: 1000 });
        if (text && text.trim().length > 0) {
          return text.trim();
        }
      } catch (e) {
        // Continue to next selector
      }
    }

    // FALLBACK: Get all text from popup and remove the title
    const popupText = await popup.textContent();
    const titleText = await popup.locator('#swal2-title').textContent().catch(() => '');
    
    // Remove title from full text
    let message = popupText.replace(titleText, '').trim();
    
    // Clean up button text if present
    const buttonTexts = await popup.locator('button').allTextContents();
    for (const btnText of buttonTexts) {
      message = message.replace(btnText, '');
    }
    
    return message.trim();
  }

  /**
   * Checks if the SweetAlert is an error alert.
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<boolean>} - True if the alert is an error alert, false otherwise.
   */
  static async isErrorAlert(page) {
    return page.locator('.swal2-icon-error').isVisible();
  }

  /**
   * Checks if the SweetAlert is a success alert.
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @returns {Promise<boolean>} - True if the alert is a success alert, false otherwise.
   */
  static async isSuccessAlert(page) {
    return page.locator('.swal2-icon-success').isVisible();
  }

  /**
   * Clicks the confirm button on the SweetAlert.
   * NOW WITH FIX: Waits for the popup to be fully visible before clicking
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @param {number} [timeout=10000] - Optional timeout in milliseconds.
   * @returns {Promise<void>}
   */
  static async clickConfirm(page, timeout = 10000) {
    // CRITICAL FIX: Wait for the SweetAlert popup container to be visible AND shown
    // SweetAlert2 adds .swal2-show class when the animation completes
    const popup = page.locator('.swal2-popup.swal2-show');
    await popup.waitFor({
      state: 'visible',
      timeout
    });

    // Now wait for the confirm button to be visible within that popup
    const confirmButton = popup.locator('.swal2-confirm');
    await confirmButton.waitFor({
      state: 'visible',
      timeout: 3000
    });
    
    // Extra verification before clicking
    await expect(confirmButton).toBeVisible();
    
    // Click with force to avoid issues with animations
    await confirmButton.click({ force: true });
    
    // Optional: Wait for the popup to disappear to confirm the action completed
    await popup.waitFor({ state: 'hidden', timeout: 3000 }).catch(() => {
      // If it doesn't disappear, that's okay - some alerts stay open
    });
  }

  /**
   * Clicks the cancel button on the SweetAlert.
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @param {number} [timeout=10000] - Optional timeout in milliseconds.
   * @returns {Promise<void>}
   */
  static async clickCancel(page, timeout = 10000) {
    const popup = page.locator('.swal2-popup.swal2-show');
    await popup.waitFor({
      state: 'visible',
      timeout
    });

    const cancelButton = popup.locator('.swal2-cancel');
    await cancelButton.waitFor({
      state: 'visible',
      timeout: 3000
    });
    
    await expect(cancelButton).toBeVisible();
    await cancelButton.click({ force: true });
  }

  /**
   * Waits for a SweetAlert to appear and be fully visible
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @param {number} [timeout=10000] - Optional timeout in milliseconds.
   * @returns {Promise<void>}
   */
  static async waitForAlert(page, timeout = 10000) {
    await page.locator('.swal2-popup.swal2-show').waitFor({
      state: 'visible',
      timeout
    });
  }

  /**
   * Waits for the SweetAlert to disappear
   * @param {import(' @playwright/test').Page} page - The Playwright page object.
   * @param {number} [timeout=10000] - Optional timeout in milliseconds.
   * @returns {Promise<void>}
   */
  static async waitForAlertToDisappear(page, timeout = 10000) {
    await page.locator('.swal2-popup').waitFor({
      state: 'hidden',
      timeout
    });
  }
}