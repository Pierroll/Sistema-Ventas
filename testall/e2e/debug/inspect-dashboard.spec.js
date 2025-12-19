import { test } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { TestUsers } from '../../fixtures/testData';

test('DEBUG: Inspeccionar elementos del dashboard', async ({ page }) => {
    const loginPage = new LoginPage(page);
    
    // Login
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    console.log('✅ Login exitoso, URL:', page.url());
    
    // Esperar que cargue
    await page.waitForTimeout(3000);
    
    // Buscar elementos que contengan el nombre del usuario
    const possibleSelectors = [
        '.dropdown-toggle',
        '.dropdown-toggle div',
        '.user-name',
        '.username',
        '[class*="user"]',
        'nav [class*="dropdown"]',
        '.navbar-nav',
    ];
    
    for (const selector of possibleSelectors) {
        const count = await page.locator(selector).count();
        console.log(`\n📍 Selector: "${selector}"`);
        console.log(`   Encontrados: ${count}`);
        
        if (count > 0) {
            const text = await page.locator(selector).first().textContent();
            console.log(`   Texto: "${text}"`);
        }
    }
    
    // Obtener TODO el HTML del navbar
    const navbarHTML = await page.locator('nav, .navbar, header').first().innerHTML();
    console.log('\n📄 HTML del navbar:');
    console.log(navbarHTML);
    
    // Screenshot
    await page.screenshot({ path: 'debug-dashboard.png', fullPage: true });
    
    console.log('\n✅ Screenshot guardado: debug-dashboard.png');
});