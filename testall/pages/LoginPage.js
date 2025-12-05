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
    await this.loginButton.click();
    await this.page.waitForSelector('.swal2-container', { state: 'hidden', timeout: 5000 });
    await this.page.waitForURL('**/home');
  }

  async loginFailure(email, password) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    await this.loginButton.click();
    await this.page.waitForSelector('#alerta:not(.d-none)', { timeout: 10000 });
  }

  async isOnLoginPage() {
    return this.loginButton.isVisible();
  }
}