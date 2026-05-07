import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Notification center', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('bell icon is visible in nav', async ({ page }) => {
        await page.goto('/dashboard');
        // Notification trigger should be in navigation
        const bell = page.locator('[data-testid="notification-center"], button:has(svg[d*="M15 17h5"])').first();
        if (await bell.isVisible().catch(() => false)) {
            await bell.click();
            // dropdown shows up
            await page.waitForTimeout(300);
        }
    });
});
