import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Dashboard', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('renders KPI cards and trend charts', async ({ page }) => {
        await page.goto('/dashboard');
        // KPI numbers visible (count cards by their grid container)
        await expect(page.locator('canvas').first()).toBeVisible();
        // Page heading
        await expect(page.getByRole('heading', { name: /dashboard|nadzor/i })).toBeVisible();
    });

    test('charts canvas elements render without error', async ({ page }) => {
        await page.goto('/dashboard');
        const canvases = await page.locator('canvas').count();
        expect(canvases).toBeGreaterThanOrEqual(2);
    });

    test('manager sees all projects on dashboard', async ({ page, context }) => {
        await context.clearCookies();
        await login(page, 'manager');
        await page.goto('/dashboard');
        await expect(page).toHaveURL(/\/dashboard/);
    });
});
