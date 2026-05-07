import { test, expect } from '@playwright/test';
import { USERS, login } from './helpers';

test.describe('Authentication', () => {
    test('login page renders', async ({ page }) => {
        await page.goto('/login');
        await expect(page.locator('input[type="email"]')).toBeVisible();
        await expect(page.locator('input[type="password"]')).toBeVisible();
    });

    test('rejects bad credentials', async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[type="email"]', 'wrong@example.com');
        await page.fill('input[type="password"]', 'wrong');
        await page.getByRole('button', { name: /log in|prijavi/i }).click();
        await expect(page).not.toHaveURL(/\/dashboard/);
    });

    test('admin can log in and reach dashboard', async ({ page }) => {
        await login(page, 'admin');
        await expect(page).toHaveURL(/\/dashboard/);
    });

    test('user can log in', async ({ page }) => {
        await login(page, 'user');
        await expect(page).toHaveURL(/\/dashboard/);
    });

    test('user can log out', async ({ page }) => {
        await login(page, 'admin');
        // Open dropdown
        await page.getByRole('button', { name: new RegExp(USERS.admin.name.split(' ')[0], 'i') }).first().click();
        // Click log out
        await page.getByRole('button', { name: /log out|odjava/i }).click();
        await expect(page).toHaveURL(/\/login/);
    });
});
