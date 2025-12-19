const { chromium } = require('@playwright/test');
const fs = require('fs');
const path = require('path');
const { TestUsers } = require('./fixtures/testData');

module.exports = async config => {
  const baseURL = 'http://localhost/venta/';
  const storageState = 'storageState.json';
  const browser = await chromium.launch();
  const page = await browser.newPage();

  try {
    await page.goto(baseURL);

    const response = await page.request.post(`${baseURL}usuarios/validar`, {
      form: {
        correo: TestUsers.admin.email,
        clave: TestUsers.admin.password,
      },
    });

    if (response.ok()) {
        await page.goto(`${baseURL}administracion/home`);
        await page.context().storageState({ path: storageState });
        console.log('Successfully logged in and saved storage state.');
    } else {
        throw new Error('Login failed');
    }

  } catch (error) {
    console.error('Failed to login during global setup:', error);
    process.exit(1);
  } finally {
    await browser.close();
  }
};