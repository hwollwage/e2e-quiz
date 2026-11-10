// @ts-check
/** @type {import('@playwright/test').PlaywrightTestConfig} */
const config = {
    testDir: './tests/e2e',
    timeout: 30_000,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    workers: 1,
    reporter: [
        ['list'],
        ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ],
    use: {
        baseURL: process.env.BASE_URL ?? 'http://127.0.0.1:8000',
        headless: true,
        screenshot: 'only-on-failure',
        trace: 'on-first-retry',
        navigationTimeout: 15_000,
    },
    projects: [
        {
            name: 'chromium',
            use: {
                executablePath: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH
                    ?? '/home/edelweiss/.cache/ms-playwright/chromium_headless_shell-1254/chrome-headless-shell-linux64/chrome-headless-shell',
            },
        },
    ],
};

module.exports = config;
