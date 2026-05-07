import { chromium } from '@playwright/test';
import path from 'path';

const BASE = 'http://localhost:54322';
const OUT  = path.resolve(__dirname, '../docs/screenshots');

async function main() {
    const browser = await chromium.launch();
    const ctx     = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const page    = await ctx.newPage();

    // Login
    await page.goto(`${BASE}/login`);
    await page.getByLabel('Email or Username').fill('ana@firma.hr');
    await page.getByLabel('Password').fill('password');
    await page.getByRole('button', { name: 'Log in' }).click();
    await page.waitForURL(`${BASE}/dashboard`);

    // Set English locale via cookie/localStorage if needed — app uses URL or session
    // Inject locale switch by navigating with ?lang=en if supported, otherwise leave as-is
    // (screenshots will be in whatever language is active for demo user)

    // 1. Dashboard
    await page.goto(`${BASE}/dashboard`);
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: `${OUT}/dashboard.png`, fullPage: false });
    console.log('✓ dashboard');

    // 2. Project list
    await page.goto(`${BASE}/projects`);
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: `${OUT}/project-list.png`, fullPage: false });
    console.log('✓ project-list');

    // 3. Kanban
    await page.goto(`${BASE}/projects/kanban`);
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: `${OUT}/kanban.png`, fullPage: false });
    console.log('✓ kanban');

    // 4. Timeline (phases on)
    await page.goto(`${BASE}/projects/timeline`);
    await page.waitForLoadState('networkidle');
    // Toggle phases on
    const phasesBtn = page.getByRole('button', { name: /phases/i });
    if (await phasesBtn.isVisible()) await phasesBtn.click();
    await page.waitForTimeout(400);
    await page.screenshot({ path: `${OUT}/timeline.png`, fullPage: false });
    console.log('✓ timeline');

    // 5. Project detail (first project)
    await page.goto(`${BASE}/projects`);
    await page.waitForLoadState('networkidle');
    const firstEdit = page.getByRole('link', { name: /edit|view/i }).first();
    if (await firstEdit.isVisible()) {
        await firstEdit.click();
        await page.waitForLoadState('networkidle');
        await page.screenshot({ path: `${OUT}/project-detail.png`, fullPage: false });
        console.log('✓ project-detail');
    }

    // 6. Dark mode — dashboard
    await ctx.close();
    const darkCtx  = await browser.newContext({ viewport: { width: 1440, height: 900 }, colorScheme: 'dark' });
    const darkPage = await darkCtx.newPage();
    await darkPage.goto(`${BASE}/login`);
    await darkPage.getByLabel('Email or Username').fill('ana@firma.hr');
    await darkPage.getByLabel('Password').fill('password');
    await darkPage.getByRole('button', { name: 'Log in' }).click();
    await darkPage.waitForURL(`${BASE}/dashboard`);
    await darkPage.waitForLoadState('networkidle');
    await darkPage.screenshot({ path: `${OUT}/dashboard-dark.png`, fullPage: false });
    console.log('✓ dashboard-dark');

    await browser.close();
    console.log(`\nAll screenshots saved to docs/screenshots/`);
}

main().catch(err => { console.error(err); process.exit(1); });
