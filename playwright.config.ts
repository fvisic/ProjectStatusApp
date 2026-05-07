import { defineConfig, devices } from '@playwright/test';

const PORT = 8001;
const BASE_URL = `http://127.0.0.1:${PORT}`;

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: false,        // shared sqlite + sessions: keep serial
    workers: 1,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    reporter: process.env.CI ? [['list'], ['html', { open: 'never' }]] : 'list',
    timeout: 30_000,
    expect: { timeout: 5_000 },

    use: {
        baseURL: BASE_URL,
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
        actionTimeout: 7_000,
        navigationTimeout: 15_000,
    },

    projects: [
        {
            name: 'chromium-light',
            use: { ...devices['Desktop Chrome'], colorScheme: 'light' },
            // Functional specs only — exclude visual-sweep and mobile-only suites.
            testIgnore: [/dark-mode\.spec\.ts$/, /mobile\.spec\.ts$/],
        },
        {
            name: 'chromium-dark',
            use: { ...devices['Desktop Chrome'], colorScheme: 'dark' },
            testMatch: /dark-mode\.spec\.ts$/,
        },
        {
            name: 'mobile',
            // iPhone 13 viewport on chromium so we don't need webkit installed.
            use: {
                ...devices['Desktop Chrome'],
                viewport: { width: 390, height: 844 },
                isMobile: true,
                hasTouch: true,
                userAgent:
                    'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 ' +
                    '(KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            },
            testMatch: /mobile\.spec\.ts$/,
        },
    ],

    globalSetup: './tests/e2e/global-setup.ts',

    webServer: {
        command: `APP_ENV=e2e php -S 127.0.0.1:${PORT} -t public`,
        url: BASE_URL,
        reuseExistingServer: !process.env.CI,
        timeout: 30_000,
        stdout: 'ignore',
        stderr: 'pipe',
    },
});
