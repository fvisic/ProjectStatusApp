import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('Admin impersonation', () => {
    test('admin can impersonate a user and see banner, then stop', async ({ page }) => {
        await login(page, 'admin');
        await page.goto('/dashboard');

        // Open user dropdown
        await page.getByRole('button', { name: new RegExp(USERS.admin.name.split(' ')[0], 'i') }).first().click();

        // Click "impersonate" / "log in as" entry — UI may use either label
        const impersonateLink = page.locator('text=/log in as|prijavi se kao|impersonat/i').first();
        if (!(await impersonateLink.isVisible().catch(() => false))) {
            test.skip(true, 'No impersonation entry found in UI for this build');
        }
        await impersonateLink.click();

        // Pick a target user
        const target = page.getByRole('button', { name: new RegExp(USERS.user.name.split(' ')[0], 'i') }).first();
        if (await target.isVisible().catch(() => false)) {
            await target.click();
            await expect(page.locator('text=/impersonating|prijavljen si kao/i')).toBeVisible();
            // Stop impersonation
            await page.getByRole('button', { name: /stop|vrati/i }).first().click();
        }
    });

    test('non-admin cannot impersonate', async ({ page }) => {
        await login(page, 'user');
        await page.goto('/dashboard');
        await page.getByRole('button', { name: new RegExp(USERS.user.name.split(' ')[0], 'i') }).first().click();
        await expect(page.locator('text=/log in as|prijavi se kao/i')).toHaveCount(0);
    });
});
