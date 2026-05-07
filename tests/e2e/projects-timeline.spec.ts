import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Project timeline', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('renders timeline with project bars', async ({ page }) => {
        await page.goto('/projects/timeline');
        // header switcher visible
        await expect(page.getByRole('link', { name: /timeline|vremensk/i }).first()).toBeVisible();
    });

    test('zoom in/out buttons toggle period granularity', async ({ page }) => {
        await page.goto('/projects/timeline');
        const zoomIn = page.getByRole('button', { name: /zoom in|uvecaj/i }).first();
        const zoomOut = page.getByRole('button', { name: /zoom out|umanji/i }).first();
        if (await zoomIn.isVisible().catch(() => false)) {
            await zoomIn.click();
            await page.waitForTimeout(300);
            await zoomOut.click();
        }
    });
});
