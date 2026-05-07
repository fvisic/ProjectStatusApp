import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Projects list', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('lists seeded projects', async ({ page }) => {
        await page.goto('/projects');
        // table or card rows present
        const rows = page.locator('table tbody tr');
        await expect(rows.first()).toBeVisible();
        const count = await rows.count();
        expect(count).toBeGreaterThan(0);
    });

    test('switches to kanban view via nav button', async ({ page }) => {
        await page.goto('/projects');
        await page.getByRole('link', { name: /kanban/i }).first().click();
        await expect(page).toHaveURL(/\/projects\/kanban/);
    });

    test('switches to timeline view via nav button', async ({ page }) => {
        await page.goto('/projects');
        await page.getByRole('link', { name: /timeline|vremensk/i }).first().click();
        await expect(page).toHaveURL(/\/projects\/timeline/);
    });

    test('opens project edit page from list', async ({ page }) => {
        await page.goto('/projects');
        // Click first row's name link
        await page.locator('table tbody tr').first().getByRole('link').first().click();
        await expect(page).toHaveURL(/\/projects\/\d+\/edit/);
    });
});
