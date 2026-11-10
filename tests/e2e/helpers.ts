import { type Page, expect } from '@playwright/test';

declare const process: { env: Record<string, string | undefined> };

export const TEST_MEMBER = {
    name    : process.env.TEST_MEMBER_NAME  ?? 'Member Test E2E',
    email   : process.env.TEST_MEMBER_EMAIL ?? 'e2e.member@gymin.test',
    password: process.env.TEST_MEMBER_PASS  ?? 'password123',
    gender  : 'male' as const,
};

export const TEST_RECEPTIONIST = {
    email   : process.env.TEST_RECEP_EMAIL ?? 'e2e.recep@gymin.test',
    password: process.env.TEST_RECEP_PASS  ?? 'password123',
};

export async function loginAs(
    page: Page,
    email: string,
    password: string,
): Promise<void> {
    await page.goto('/login');
    await page.locator('input[name="email"]').fill(email);
    await page.locator('input[name="password"]').fill(password);
    await page.locator('button[type="submit"]').click();
    await page.waitForURL(/\/dashboard/, { timeout: 10_000 });
}

export async function logout(page: Page): Promise<void> {
    await page.evaluate(async () => {
        const token = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? '';
        await fetch('/logout', {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' },
        });
    });
    await page.goto('/login');
}

export async function expectSuccessAlert(page: Page): Promise<void> {
    await expect(
        page.locator('.alert-success, [class*="alert-success"]').first(),
    ).toBeVisible({ timeout: 8_000 });
}

export async function expectErrorAlert(page: Page): Promise<void> {
    await expect(
        page.locator('.alert-error, [style*="f87171"]').first(),
    ).toBeVisible({ timeout: 8_000 });
}
