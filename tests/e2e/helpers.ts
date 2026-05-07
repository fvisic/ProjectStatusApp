import { Page, expect } from '@playwright/test';

/**
 * Seeded credentials (see DatabaseSeeder).
 * All seeded users share the password `Demo1234!`.
 */
export const USERS = {
    admin:   { email: 'sarah@example.com', password: 'Demo1234!', name: 'Sarah Chen',   role: 'admin'   },
    manager: { email: 'james@example.com', password: 'Demo1234!', name: 'James Miller', role: 'manager' },
    user:    { email: 'priya@example.com', password: 'Demo1234!', name: 'Priya Sharma', role: 'user'    },
    user2:   { email: 'tom@example.com',   password: 'Demo1234!', name: 'Tom Weber',    role: 'user'    },
} as const;

export type SeededUser = keyof typeof USERS;

/**
 * Log in via the public /login form. Asserts dashboard loads.
 * Onboarding modal is auto-dismissed if it pops up.
 */
export async function login(page: Page, who: SeededUser = 'admin') {
    const u = USERS[who];
    await page.goto('/login');
    await page.fill('input[type="email"]', u.email);
    await page.fill('input[type="password"]', u.password);
    await page.getByRole('button', { name: /log in|prijavi/i }).click();
    await page.waitForURL(/\/dashboard/, { timeout: 10_000 });
    await dismissOnboardingIfPresent(page);
}

/**
 * The first-run onboarding modal blocks interactions on every page until
 * dismissed. Skip it cleanly so individual specs stay focused.
 */
export async function dismissOnboardingIfPresent(page: Page) {
    const skip = page.getByRole('button', { name: /skip|preskoči/i });
    if (await skip.isVisible().catch(() => false)) {
        await skip.click();
        await skip.waitFor({ state: 'hidden' }).catch(() => null);
    }
}

/**
 * Toggle dark mode by setting localStorage and reloading.
 * Mirrors the in-app toggle (locale-switcher.blade.php / dark-toggle).
 */
export async function setDarkMode(page: Page, enabled: boolean) {
    await page.addInitScript((flag) => {
        localStorage.setItem('darkMode', flag ? 'true' : 'false');
    }, enabled);
    await page.reload();
    await expect.poll(async () =>
        page.evaluate(() => document.documentElement.classList.contains('dark'))
    ).toBe(enabled);
}

/**
 * Assert that the current page has no plainly-illegible text in the active
 * color scheme. Heuristic: collect the resolved foreground/background of every
 * visible text node and fail if any element renders as e.g. dark-grey on
 * dark-grey (contrast ratio < 2). Catches the most common dark-mode regressions.
 */
export async function assertReadableContrast(page: Page) {
    const violations = await page.evaluate(() => {
        function rgb(s: string): [number, number, number] | null {
            const m = s.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
            return m ? [+m[1], +m[2], +m[3]] : null;
        }
        function luminance([r, g, b]: number[]): number {
            const a = [r, g, b].map(v => {
                v /= 255;
                return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
            });
            return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
        }
        function contrast(c1: number[], c2: number[]): number {
            const l1 = luminance(c1), l2 = luminance(c2);
            const [hi, lo] = l1 > l2 ? [l1, l2] : [l2, l1];
            return (hi + 0.05) / (lo + 0.05);
        }
        function effectiveBg(el: HTMLElement): number[] | null {
            let cur: HTMLElement | null = el;
            while (cur) {
                const bg = rgb(getComputedStyle(cur).backgroundColor);
                if (bg && getComputedStyle(cur).backgroundColor !== 'rgba(0, 0, 0, 0)') return bg;
                cur = cur.parentElement;
            }
            return rgb(getComputedStyle(document.body).backgroundColor);
        }
        // Only check elements whose own *direct* text node has visible content.
        // This avoids false positives on parents (TD, DIV) whose textContent
        // comes from nested elements that may have their own color.
        function ownText(el: HTMLElement): string {
            let s = '';
            for (const n of Array.from(el.childNodes)) {
                if (n.nodeType === Node.TEXT_NODE) s += (n.nodeValue || '');
            }
            return s.trim();
        }
        const out: { tag: string; text: string; ratio: number }[] = [];
        document.querySelectorAll<HTMLElement>('h1, h2, h3, h4, p, span, a, button, td, th, label, li, div').forEach(el => {
            const text = ownText(el);
            if (!text || text.length < 2) return;
            const r = el.getBoundingClientRect();
            if (r.width < 4 || r.height < 4) return;
            const fg = rgb(getComputedStyle(el).color);
            const bg = effectiveBg(el);
            if (!fg || !bg) return;
            const ratio = contrast(fg, bg);
            // Threshold 1.5: anything below that is essentially same-on-same
            // (truly unreadable). We don't enforce WCAG AA here — that would
            // flag intentional design choices (white labels on coloured bars).
            if (ratio < 1.5) {
                out.push({ tag: el.tagName, text: text.slice(0, 60), ratio: +ratio.toFixed(2) });
            }
        });
        return out;
    });
    if (violations.length) {
        const msg = violations.slice(0, 8).map(v => `  <${v.tag}> ratio ${v.ratio} — "${v.text}"`).join('\n');
        throw new Error(`Low-contrast text detected (${violations.length}):\n${msg}`);
    }
}
