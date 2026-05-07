import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Documentation', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('docs page loads', async ({ page }) => {
        await page.goto('/docs');
        await expect(page.locator('main, .prose, article').first()).toBeVisible();
    });
});
