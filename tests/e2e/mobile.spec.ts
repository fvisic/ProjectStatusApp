import { test, expect } from '@playwright/test';
import { login } from './helpers';

const PAGES = [
    '/dashboard',
    '/projects',
    '/projects/kanban',
    '/projects/timeline',
    '/projects/1/edit',
    '/profile',
    '/docs',
];

test.describe('Mobile viewport', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    for (const path of PAGES) {
        test(`${path} — no horizontal scroll on iPhone viewport`, async ({ page }) => {
            await page.goto(path);
            await page.waitForLoadState('networkidle').catch(() => null);
            // Allow up to 4px wiggle for scrollbars/anti-aliasing.
            const overflow = await page.evaluate(
                () => document.documentElement.scrollWidth - document.documentElement.clientWidth
            );
            expect(overflow, `Page ${path} has horizontal overflow of ${overflow}px`).toBeLessThanOrEqual(4);
        });
    }

    test('hamburger menu opens nav drawer', async ({ page }) => {
        await page.goto('/dashboard');
        const hamburger = page.locator('button:has(svg path[d^="M4 6h16"])').first();
        await hamburger.click();
        // After click, nav links should become visible
        await expect(page.getByRole('link', { name: /dashboard/i }).first()).toBeVisible();
    });
});
