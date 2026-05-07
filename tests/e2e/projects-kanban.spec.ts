import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Project kanban', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('renders three health columns', async ({ page }) => {
        await page.goto('/projects/kanban');
        await expect(page.locator('[data-health="on_track"]')).toBeVisible();
        await expect(page.locator('[data-health="at_risk"]')).toBeVisible();
        await expect(page.locator('[data-health="off_track"]')).toBeVisible();
    });

    test('shows project cards in columns', async ({ page }) => {
        await page.goto('/projects/kanban');
        await expect(page.locator('.kanban-card').first()).toBeVisible();
    });
});
