import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Project create', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('renders create form with name input', async ({ page }) => {
        await page.goto('/projects/create');
        await expect(page.locator('input[wire\\:model="name"]').first()).toBeVisible();
    });

    test('creates a new project end-to-end', async ({ page }) => {
        await page.goto('/projects/create');

        const unique = `E2E Project ${Date.now()}`;
        await page.locator('input[wire\\:model="name"]').first().fill(unique);
        await page.locator('input[wire\\:model="client"]').first().fill('E2E Client');

        await page.getByRole('button', { name: /save|spremi/i }).first().click();

        // Save redirects to /projects with a flash message.
        await page.waitForURL(/\/projects\b/, { timeout: 10_000 });
        await expect(page.locator(`text=${unique}`).first()).toBeVisible();
    });
});
