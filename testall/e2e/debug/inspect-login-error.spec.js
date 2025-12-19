import { test } from '@playwright/test';
import { TestUsers } from '../../fixtures/testData';

test('DEBUG: Ver alerta de error de login', async ({ page }) => {
    await page.goto('http://localhost/venta/administracion/');
    
    console.log('URL ANTES del login:', page.url());
    
    // Login incorrecto
    await page.fill('input[name="correo"]', TestUsers.admin.email);
    await page.fill('input[name="clave"]', 'wrongpassword');
    await page.click('#btnAccion');
    
    // Esperar 3 segundos
    await page.waitForTimeout(3000);
    
    console.log('URL DESPUÉS del login:', page.url());
    
    // Ver TODO el HTML de #alerta con sus atributos
    const alertaExists = await page.locator('#alerta').count();
    console.log('\n📍 #alerta existe:', alertaExists > 0);
    
    if (alertaExists > 0) {
        const alertaHTML = await page.locator('#alerta').innerHTML();
        const alertaClasses = await page.locator('#alerta').getAttribute('class');
        const alertaStyles = await page.locator('#alerta').getAttribute('style');
        const alertaVisible = await page.locator('#alerta').isVisible();
        
        console.log('   HTML interno:', alertaHTML);
        console.log('   Clases:', alertaClasses);
        console.log('   Estilos inline:', alertaStyles);
        console.log('   Visible:', alertaVisible);
    }
    
    // Ver si hay parámetros en la URL
    console.log('\n🔗 URL completa:', page.url());
    
    // Ver todas las alertas en la página
    console.log('\n📋 Todas las alertas:');
    const allAlerts = await page.locator('.alert, [class*="alert"]').all();
    for (let i = 0; i < allAlerts.length; i++) {
        const text = await allAlerts[i].textContent();
        const classes = await allAlerts[i].getAttribute('class');
        const visible = await allAlerts[i].isVisible();
        console.log(`   Alert ${i}: visible=${visible}, classes="${classes}", text="${text.trim()}"`);
    }
    
    // Screenshot
    await page.screenshot({ path: 'debug-login-error.png', fullPage: true });
    console.log('\n✅ Screenshot: debug-login-error.png');
    
    // Ver el HTML completo de la página
    const bodyHTML = await page.locator('body').innerHTML();
    console.log('\n📄 Buscando "alerta" en el HTML...');
    const alertaMatches = bodyHTML.match(/id="alerta"[^>]*>/g);
    if (alertaMatches) {
        console.log('   Encontrado:', alertaMatches[0]);
    }
});