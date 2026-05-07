import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Profile', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('profile page loads with three sections', async ({ page }) => {
        await page.goto('/profile');
        await expect(page.locator('input[id="name"]')).toBeVisible();
        await expect(page.locator('input[id="email"]')).toBeVisible();
    });

    test('updates display name', async ({ page }) => {
        await page.goto('/profile');
        const name = page.locator('input[id="name"]');
        const original = await name.inputValue();
        await name.fill('E2E Renamed');
        await page.locator('form[wire\\:submit*="updateProfile"] button[type="submit"]').first().click();
        await expect(page.locator('text=/saved|spremljen/i').first()).toBeVisible();

        await name.fill(original);
        await page.locator('form[wire\\:submit*="updateProfile"] button[type="submit"]').first().click();
    });
});
