const { chromium } = require('@playwright/test');
const path = require('path');

const BASE = 'http://localhost:54322';
const OUT  = path.resolve(__dirname, '../docs/screenshots');
const TABS = ['basic', 'phases', 'estimation', 'risks', 'burndown', 'comments'];

async function createContext(browser, dark = false) {
    const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    if (dark) {
        await ctx.addInitScript(() => localStorage.setItem('darkMode', 'true'));
    }
    return ctx;
}

async function doLogin(page) {
    await page.goto(`${BASE}/login`);
    await page.locator('#login').fill('ana@firma.hr');
    await page.locator('#password').fill('password');
    await page.locator('button[type="submit"]').click();
    await page.waitForURL(`${BASE}/dashboard`);
}

async function shot(page, name) {
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: `${OUT}/${name}.png` });
    console.log(`✓ ${name}`);
}

async function dismissOnboarding(page) {
    const modal = page.locator('[wire\\:key="onboarding"]');
    if (await modal.isVisible({ timeout: 2000 }).catch(() => false)) {
        const skipBtn = modal.locator('button[wire\\:click="skip"]');
        if (await skipBtn.isVisible({ timeout: 1000 }).catch(() => false)) {
            await skipBtn.click();
        }
        await modal.waitFor({ state: 'hidden', timeout: 5000 }).catch(() => {});
    }
}

async function switchToEnglish(page) {
    const enBtn = page.getByRole('button', { name: 'EN' });
    if (await enBtn.isVisible({ timeout: 3000 }).catch(() => false)) {
        await enBtn.click();
        await page.waitForLoadState('networkidle');
    }
}

async function findBestProjectId(page) {
    await page.goto(`${BASE}/projects`);
    await page.waitForLoadState('networkidle');
    await dismissOnboarding(page);
    const links = await page.locator('a[href*="/projects/"][href*="/edit"]').all();
    // pick the last one — seeder tends to fill later projects more
    const href = await links[links.length - 1].getAttribute('href');
    return href.match(/\/projects\/(\d+)\/edit/)?.[1];
}

async function main() {
    const browser = await chromium.launch();

    // --- Light mode ---
    const ctx  = await createContext(browser);
    const page = await ctx.newPage();
    await doLogin(page);

    // Switch to English (dismiss onboarding first)
    await page.goto(`${BASE}/dashboard`);
    await page.waitForLoadState('networkidle');
    await dismissOnboarding(page);
    await switchToEnglish(page);

    // Dashboard
    await page.goto(`${BASE}/dashboard`);
    await shot(page, 'dashboard');

    // Project list
    await page.goto(`${BASE}/projects`);
    await dismissOnboarding(page);
    await shot(page, 'project-list');

    // Kanban
    await page.goto(`${BASE}/projects/kanban`);
    await dismissOnboarding(page);
    await shot(page, 'kanban');

    // Timeline with phases on
    await page.goto(`${BASE}/projects/timeline`);
    await page.waitForLoadState('networkidle');
    const phasesBtn = page.getByRole('button', { name: /phases/i });
    if (await phasesBtn.isVisible({ timeout: 3000 }).catch(() => false)) {
        await phasesBtn.click();
        await page.waitForTimeout(500);
    }
    await page.screenshot({ path: `${OUT}/timeline.png` });
    console.log('✓ timeline');

    // Project detail — all tabs (click each tab button explicitly)
    const projectId = await findBestProjectId(page);
    await page.goto(`${BASE}/projects/${projectId}/edit`);
    await page.waitForLoadState('networkidle');
    for (const tab of TABS) {
        await page.locator(`button[\\@click="activeTab = '${tab}'"]`).click();
        await page.waitForTimeout(500);
        await page.screenshot({ path: `${OUT}/project-${tab}.png` });
        console.log(`✓ project-${tab}`);
    }

    await ctx.close();

    // --- Dark mode (dashboard) ---
    const dCtx  = await createContext(browser, true);
    const dPage = await dCtx.newPage();
    await doLogin(dPage);
    await dPage.goto(`${BASE}/dashboard`);
    await shot(dPage, 'dashboard-dark');
    await dCtx.close();

    await browser.close();
    console.log(`\nDone → docs/screenshots/`);
}

main().catch(err => { console.error(err); process.exit(1); });
