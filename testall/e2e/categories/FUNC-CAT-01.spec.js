import { test, expect } from '@playwright/test';
import { LoginPage } from '../../pages/LoginPage';
import { DashboardPage } from '../../pages/DashboardPage';
import { CategoriesPage } from '../../pages/CategoriesPage';
import { TestUsers } from '../../fixtures/testData';

test.describe('Categories Management', () => {

  test('FUNC-CAT-01: Create a new category', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboardPage = new DashboardPage(page);
    const categoriesPage = new CategoriesPage(page);

    // --- 1. Iniciar sesión como Admin ---
    await loginPage.goto();
    await loginPage.loginSuccess(TestUsers.admin.email, TestUsers.admin.password);
    
    // --- 2. Navegar a la página de categorías desde el dashboard ---
    await dashboardPage.navigateToCategories();
    
    // --- 3. Generar un nombre único para la nueva categoría ---
    const randomId = Date.now();
    const categoryName = `Categoria Test ${randomId}`;

    // --- 4. Crear la categoría ---
    await categoriesPage.createCategory(categoryName);
    
    // --- 5. Verificar que la nueva categoría aparece en la tabla ---
    const isCategoryVisible = await categoriesPage.isCategoryInTable(categoryName);
    expect(isCategoryVisible, `La categoría '${categoryName}' debe ser visible en la tabla`).toBe(true);

    console.log(`✅ Categoría '${categoryName}' creada y verificada exitosamente.`);
  });

});