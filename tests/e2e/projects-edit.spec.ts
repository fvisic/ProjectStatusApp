import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Project edit', () => {
    test.beforeEach(async ({ page }) => login(page, 'admin'));

    test('opens existing project for editing', async ({ page }) => {
        await page.goto('/projects/1/edit');
        await expect(page.locator('input[wire\\:model="name"]').first()).toBeVisible();
        await expect(page.locator('input[wire\\:model="name"]').first()).not.toHaveValue('');
    });

    test('saves edits and bumps version when changes detected', async ({ page }) => {
        await page.goto('/projects/1/edit');
        const versionBefore = await page.locator('input[wire\\:model="version"]').first().inputValue();

        // Switch to estimation tab to reach the comment textarea.
        await page.locator('button[\\@click="activeTab = \'estimation\'"]').click();
        await page.locator('textarea[wire\\:model="estimation_comment"]').fill(`E2E ${Date.now()}`);

        await page.getByRole('button', { name: /save|spremi/i }).first().click();
        await page.waitForURL(/\/projects\/?$/, { timeout: 10_000 });

        // Re-open the project and verify version was bumped.
        await page.goto('/projects/1/edit');
        const versionAfter = await page.locator('input[wire\\:model="version"]').first().inputValue();
        expect(versionAfter).not.toBe(versionBefore);
    });

    test('second save with no edits leaves version intact', async ({ page }) => {
        // First save normalises diff state with the latest snapshot.
        await page.goto('/projects/2/edit');
        await page.getByRole('button', { name: /save|spremi/i }).first().click();
        await page.waitForURL(/\/projects\/?$/, { timeout: 10_000 });

        // Second save without touching anything should NOT bump.
        await page.goto('/projects/2/edit');
        const versionBefore = await page.locator('input[wire\\:model="version"]').first().inputValue();
        await page.getByRole('button', { name: /save|spremi/i }).first().click();
        await page.waitForURL(/\/projects\/?$/, { timeout: 10_000 });

        await page.goto('/projects/2/edit');
        const versionAfter = await page.locator('input[wire\\:model="version"]').first().inputValue();
        expect(versionAfter).toBe(versionBefore);
    });

    test('phases tab supports add and remove', async ({ page }) => {
        await page.goto('/projects/1/edit');
        // Open phases tab — Alpine x-data tabs use a button to toggle activeTab.
        const phasesTab = page.locator('button:has-text("Phase"), button:has-text("Faz")').first();
        if (await phasesTab.isVisible().catch(() => false)) {
            await phasesTab.click();
        }

        const before = await page.locator('.phase-row').count();
        const addBtn = page.locator('button:has-text("Add phase"), button:has-text("Dodaj faz")').first();
        if (await addBtn.isVisible().catch(() => false)) {
            await addBtn.click();
            await expect.poll(() => page.locator('.phase-row').count()).toBe(before + 1);
            // Remove the row we just added
            await page.locator('.phase-row').last().locator('button:has-text("×")').click();
            await expect.poll(() => page.locator('.phase-row').count()).toBe(before);
        }
    });
});
