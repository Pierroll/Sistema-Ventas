import { test } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { TestUsers } from '../../fixtures/testData';

test('DEBUG: Verificar página de usuarios', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);

    // Login como admin
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    await dashboardPage.isOnDashboard();

    console.log('✅ Login exitoso');
    console.log('URL actual:', page.url());

    // Probar diferentes URLs
    const urlsToTry = [
        'http://localhost/venta/administracion/usuarios',
        'administracion/usuarios',
        '/administracion/usuarios',
        'usuarios',
    ];

    for (const url of urlsToTry) {
        try {
            console.log(`\n🔍 Probando: ${url}`);
            await page.goto(url);
            await page.waitForTimeout(2000);
            
            const finalUrl = page.url();
            console.log(`   ✅ URL final: ${finalUrl}`);
            
            const hasTable = await page.locator('table').count();
            console.log(`   Tablas encontradas: ${hasTable}`);
            
            if (hasTable > 0) {
                const rows = await page.locator('table tbody tr').count();
                console.log(`   Filas en la tabla: ${rows}`);
            }
            
            const pageTitle = await page.title();
            console.log(`   Título: ${pageTitle}`);
            
            await page.screenshot({ 
                path: `debug-usuarios-${urlsToTry.indexOf(url)}.png`,
                fullPage: true 
            });
            
            console.log(`   Screenshot: debug-usuarios-${urlsToTry.indexOf(url)}.png`);
            
        } catch (error) {
            console.log(`   ❌ Error: ${error.message}`);
        }
    }
});