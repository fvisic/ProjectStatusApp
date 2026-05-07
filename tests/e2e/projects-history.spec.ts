import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Project history', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('history page lists snapshots', async ({ page }) => {
        await page.goto('/projects/1/history');
        // list of snapshots visible (links or rows with version numbers)
        await expect(page.locator('text=/v\\d+\\.\\d+/').first()).toBeVisible();
    });

    test('selecting two snapshots shows compare action', async ({ page }) => {
        await page.goto('/projects/1/history');
        const snapshots = page.locator('input[type="checkbox"]');
        const count = await snapshots.count();
        if (count >= 2) {
            await snapshots.nth(0).check();
            await snapshots.nth(1).check();
            await expect(page.getByRole('button', { name: /compare|usporedi/i }).first()).toBeVisible();
        }
    });
});
