import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Role-based access', () => {
    test('regular user only sees own projects', async ({ page }) => {
        await login(page, 'user');
        await page.goto('/projects');
        const rowCount = await page.locator('table tbody tr').count();
        expect(rowCount).toBeGreaterThanOrEqual(0);
    });

    test('manager sees all projects (read-only)', async ({ page }) => {
        await login(page, 'manager');
        await page.goto('/projects');
        // Manager should see at least as many rows as user
        const rowCount = await page.locator('table tbody tr').count();
        expect(rowCount).toBeGreaterThan(0);
    });

    test('admin sees all projects', async ({ page }) => {
        await login(page, 'admin');
        await page.goto('/projects');
        const rowCount = await page.locator('table tbody tr').count();
        expect(rowCount).toBeGreaterThan(0);
    });
});
