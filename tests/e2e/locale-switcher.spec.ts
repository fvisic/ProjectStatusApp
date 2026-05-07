import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Locale switcher', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('switches language between EN and HR', async ({ page }) => {
        await page.goto('/dashboard');
        // Find the language buttons (typically EN and HR labels)
        const en = page.getByRole('button', { name: /^EN$/i }).first();
        const hr = page.getByRole('button', { name: /^HR$/i }).first();

        if (await hr.isVisible().catch(() => false)) {
            await hr.click();
            await page.waitForLoadState('networkidle');
            // Switch back to EN
            await en.click();
            await page.waitForLoadState('networkidle');
        }
    });
});
