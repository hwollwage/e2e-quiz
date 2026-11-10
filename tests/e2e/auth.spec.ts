import { test, expect } from '@playwright/test';
import { TEST_MEMBER, loginAs, logout } from './helpers';

const uniqueEmail = `e2e_${Date.now()}@gymin.test`;

test.describe('Auth — Registrasi & Login', () => {

    test('1. Halaman login dapat diakses', async ({ page }) => {
        await page.goto('/login');
        await expect(page).toHaveURL(/\/login/);
        await expect(page.locator('input[name="email"]')).toBeVisible();
        await expect(page.locator('input[name="password"]')).toBeVisible();
        await expect(page.locator('button[type="submit"]')).toBeVisible();
    });

    test('2. Register akun member baru berhasil', async ({ page }) => {
        await page.goto('/register');

        await page.locator('input[name="name"]').fill('Playwright User');
        await page.locator('input[name="email"]').fill(uniqueEmail);
        await page.locator('select[name="gender"]').selectOption('male');
        await page.locator('input[name="password"]').fill('password123');
        await page.locator('input[name="password_confirmation"]').fill('password123');
        await page.locator('button[type="submit"]').click();

        await page.waitForURL(/\/dashboard/, { timeout: 10_000 });
        await expect(page).toHaveURL(/\/dashboard/);
    });

    test('3. Register dengan email duplikat menampilkan error', async ({ page }) => {
        await page.goto('/register');

        await page.locator('input[name="name"]').fill('Playwright User');
        await page.locator('input[name="email"]').fill(uniqueEmail);
        await page.locator('select[name="gender"]').selectOption('male');
        await page.locator('input[name="password"]').fill('password123');
        await page.locator('input[name="password_confirmation"]').fill('password123');
        await page.locator('button[type="submit"]').click();

        await expect(page).toHaveURL(/\/register/);
        await expect(page.locator('body')).toContainText(/email/i);
    });

    test('4. Login dengan password salah menampilkan error', async ({ page }) => {
        await page.goto('/login');

        await page.locator('input[name="email"]').fill(uniqueEmail);
        await page.locator('input[name="password"]').fill('wrong_password!');
        await page.locator('button[type="submit"]').click();

        await expect(page).toHaveURL(/\/login/);
        await expect(page.locator('body')).toContainText(/salah/i);
    });

    test('5. Login dengan kredensial benar berhasil', async ({ page }) => {
        await loginAs(page, uniqueEmail, 'password123');

        await expect(page).toHaveURL(/\/dashboard/);
        await expect(page.locator('body')).toContainText('Playwright User');
    });

    test('6. Guest yang akses halaman protected di-redirect ke /login', async ({ page }) => {
        await page.goto('/login');
        await page.context().clearCookies();

        await page.goto('/reservasi');
        await page.waitForURL(/\/login/, { timeout: 8_000 });
        await expect(page).toHaveURL(/\/login/);
    });

    test('7. Tombol logout mengakhiri sesi', async ({ page }) => {
        await loginAs(page, uniqueEmail, 'password123');
        await expect(page).toHaveURL(/\/dashboard/);

        await page.locator('form[action*="logout"] button, button[form*="logout"]')
            .first()
            .click()
            .catch(async () => {
                await logout(page);
            });

        await page.waitForURL(/\/login|\//, { timeout: 8_000 });
        await page.goto('/dashboard');
        await expect(page).toHaveURL(/\/login/);
    });
});
