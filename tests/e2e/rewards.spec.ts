import { test, expect } from '@playwright/test';
import { loginAs } from './helpers';

const MEMBER_EMAIL = `e2e_rwd_${Date.now()}@gymin.test`;
const MEMBER_PASS  = 'password123';

test.beforeAll(async ({ browser }) => {
    const page = await browser.newPage();
    await page.goto('/register');
    await page.locator('input[name="name"]').fill('Reward Tester');
    await page.locator('input[name="email"]').fill(MEMBER_EMAIL);
    await page.locator('select[name="gender"]').selectOption('male');
    await page.locator('input[name="password"]').fill(MEMBER_PASS);
    await page.locator('input[name="password_confirmation"]').fill(MEMBER_PASS);
    await page.locator('button[type="submit"]').click();
    await page.waitForURL(/\/dashboard/, { timeout: 10_000 });
    await page.close();
});

test.describe('Rewards — Poin & Penukaran Hadiah', () => {

    test('1. Halaman rewards dapat diakses', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/dashboard/rewards');

        await expect(page).toHaveURL(/\/dashboard\/rewards/);
    });

    test('2. Daftar reward aktif ditampilkan', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/dashboard/rewards');

        const rewardCards = page.locator('.reward-card, [class*="reward"]').filter({
            hasText: /pts|poin/i,
        });
        await expect(rewardCards.first()).toBeVisible({ timeout: 8_000 });
    });

    test('3. Poin dan streak member tampil di halaman rewards', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/dashboard/rewards');

        await expect(page.locator('.point-banner, [class*="point"]').first())
            .toBeVisible({ timeout: 8_000 });

        await expect(page.locator('body')).toContainText(/\d+\s*(pts|poin|points)/i);
    });

    test('4. Member tanpa cukup poin tidak bisa redeem', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/dashboard/rewards');

       const redeemBtns = page.locator('a[href*="redeem-ticket"], button[disabled]');
        const count = await redeemBtns.count();

        if (count > 0) {
            const disabledCount = await page.locator('button[disabled]').count();
            const labelInsufficient = await page.locator('body').textContent();
            const hasInsufficientLabel = /tidak cukup|kurang|insufficient/i.test(labelInsufficient ?? '');

            expect(disabledCount > 0 || hasInsufficientLabel).toBeTruthy();
        } else {
           test.skip();
        }
    });

    test('5. Halaman rewards tidak bisa diakses tanpa login', async ({ page }) => {
        await page.context().clearCookies();
        await page.goto('/dashboard/rewards');

        await page.waitForURL(/\/login/, { timeout: 8_000 });
        await expect(page).toHaveURL(/\/login/);
    });

    test('6. Halaman tiket redeem menampilkan QR untuk member dengan poin', async ({ page }) => {
       await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);

        await page.goto('/dashboard/rewards');

        const firstRedeemLink = page.locator('a[href*="redeem-ticket"]').first();
        const hasLink = (await firstRedeemLink.count()) > 0;

        if (!hasLink) {
            test.skip();
            return;
        }

        const href = await firstRedeemLink.getAttribute('href') ?? '';
        await page.goto(href);

        const hasQr = await page.locator('canvas, img[alt*="qr" i], #qrCanvas').count() > 0;
        expect(hasQr).toBeTruthy();
    });

});
