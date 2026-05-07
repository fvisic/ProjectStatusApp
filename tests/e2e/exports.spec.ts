import { test, expect } from '@playwright/test';
import { login } from './helpers';

/** Trigger a download by clicking a programmatically-injected anchor. */
async function clickDownload(page: import('@playwright/test').Page, url: string) {
    const dlPromise = page.waitForEvent('download');
    await page.evaluate((href) => {
        const a = document.createElement('a');
        a.href = href;
        a.download = '';
        document.body.appendChild(a);
        a.click();
    }, url);
    return dlPromise;
}

test.describe('Exports', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, 'admin');
        await page.goto('/projects'); // anchor host page
    });

    test('CSV export downloads', async ({ page }) => {
        const dl = await clickDownload(page, '/projects/export/csv');
        expect(dl.suggestedFilename()).toMatch(/projects.*\.csv$/);
    });

    test('XLSX export downloads', async ({ page }) => {
        const dl = await clickDownload(page, '/projects/export/xlsx');
        expect(dl.suggestedFilename()).toMatch(/projects.*\.xlsx$/);
    });

    test('portfolio PDF route is reachable', async ({ page, request }) => {
        const cookies = await page.context().cookies();
        const cookieHeader = cookies.map(c => `${c.name}=${c.value}`).join('; ');
        const res = await request.get('/projects/portfolio-pdf', { headers: { cookie: cookieHeader } });
        expect(res.status()).toBe(200);
    });

    test('project PDF route is reachable', async ({ page, request }) => {
        const cookies = await page.context().cookies();
        const cookieHeader = cookies.map(c => `${c.name}=${c.value}`).join('; ');
        const res = await request.get('/projects/1/pdf', { headers: { cookie: cookieHeader } });
        expect(res.status()).toBe(200);
    });
});
