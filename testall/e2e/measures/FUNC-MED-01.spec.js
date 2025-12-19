import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { MeasuresPage } from '../../pages/MeasuresPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Measures Management', () => {

  test('FUNC-MED-01: Create a new measure', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const measuresPage = new MeasuresPage(page);

    // --- 1. Iniciar sesión como Admin ---
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    // --- 2. Navegar a la página de medidas desde el dashboard ---
    await dashboardPage.navigateToMeasures();
    
    // --- 3. Generar un nombre único para la nueva medida ---
    const randomId = Date.now();
    const measureName = `Medida Test ${randomId}`;
    const measureShortName = `MT${randomId.toString().substring(0, 3)}`;

    // --- 4. Crear la medida ---
    await measuresPage.createMeasure(measureName, measureShortName);
    
    // --- 5. Verificar que la nueva medida aparece en la tabla ---
    const isMeasureVisible = await measuresPage.isMeasureInTable(measureName);
    expect(isMeasureVisible, `La medida '${measureName}' debe ser visible en la tabla`).toBe(true);

    console.log(`✅ Medida '${measureName}' (${measureShortName}) creada y verificada exitosamente.`);
  });

});
