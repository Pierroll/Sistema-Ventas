import { expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class LoginPage extends BasePage {
  constructor(page) {
    super(page);
    this.emailInput = page.locator('#frmLogin input[name="correo"]');
    this.passwordInput = page.locator('input[name="clave"]');
    this.loginButton = page.locator('#btnAccion');
    this.alert = page.locator('#alerta');
  }

  async goto() {
    await this.page.goto('http://localhost/venta/administracion/');
    await this.page.waitForLoadState('networkidle');
  }

  async loginSuccess(email, password) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    // Wait for the navigation to complete after clicking
    await Promise.all([
        this.page.waitForNavigation({ waitUntil: 'networkidle' }),
        this.loginButton.click()
    ]);
  }

  async loginFailure(email, password) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    await this.loginButton.click();
    // Wait for the SweetAlert modal to be visible
    await this.page.waitForSelector('.swal2-container.swal2-center.swal2-backdrop-show', { state: 'visible', timeout: 10000 });
    // Assert that the error icon is visible
    await expect(this.page.locator('.swal2-icon-error')).toBeVisible();
  }

  async isOnLoginPage() {
    return this.loginButton.isVisible();
  }
}