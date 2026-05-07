import { test } from '@playwright/test';
import { login, setDarkMode, assertReadableContrast } from './helpers';

const PAGES = [
    { path: '/dashboard',           name: 'Dashboard' },
    { path: '/projects',            name: 'Projects list' },
    { path: '/projects/kanban',     name: 'Projects kanban' },
    { path: '/projects/timeline',   name: 'Projects timeline' },
    { path: '/projects/create',     name: 'Project create' },
    { path: '/projects/1/edit',     name: 'Project edit' },
    { path: '/projects/1/history',  name: 'Project history' },
    { path: '/profile',             name: 'Profile' },
    { path: '/docs',                name: 'Documentation' },
];

test.describe('Dark mode visual sweep', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, 'admin');
        await setDarkMode(page, true);
    });

    for (const { path, name } of PAGES) {
        test(`${name} (${path}) — no low-contrast text in dark mode`, async ({ page }) => {
            await page.goto(path);
            await page.waitForLoadState('networkidle').catch(() => null);
            await assertReadableContrast(page);
        });
    }
});
