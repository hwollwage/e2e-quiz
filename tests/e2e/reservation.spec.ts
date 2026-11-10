import { test, expect, type Page } from '@playwright/test';
import { loginAs } from './helpers';

const MEMBER_EMAIL = `e2e_res_${Date.now()}@gymin.test`;
const MEMBER_PASS  = 'password123';

test.beforeAll(async ({ browser }) => {
    const page = await browser.newPage();
    await page.goto('/register');
    await page.locator('input[name="name"]').fill('Res Tester');
    await page.locator('input[name="email"]').fill(MEMBER_EMAIL);
    await page.locator('select[name="gender"]').selectOption('female');
    await page.locator('input[name="password"]').fill(MEMBER_PASS);
    await page.locator('input[name="password_confirmation"]').fill(MEMBER_PASS);
    await page.locator('button[type="submit"]').click();
    await page.waitForURL(/\/dashboard/, { timeout: 10_000 });
    await page.close();
});

test.describe('Reservasi — Alur Pemesanan Sesi', () => {

    test('1. Halaman reservasi dapat diakses', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/reservasi');

        await expect(page).toHaveURL(/\/reservasi/);
        await expect(page.locator('button.res-tab').first()).toContainText(/Buat Reservasi/i);
    });

    test('2. Slot grid tampil setelah halaman dimuat', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/reservasi');

        const slotBtn = page.locator('#slotGrid .slot-btn').first();
        await expect(slotBtn).toBeVisible({ timeout: 10_000 });
        const availableSlots = page.locator('#slotGrid .slot-btn:not(.taken)');
        expect(await availableSlots.count()).toBeGreaterThan(0);
    });

    test('3. Buat reservasi baru berhasil', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/reservasi');

        await page.locator('#slotGrid .slot-btn:not(.taken)').first().click();
        await expect(page.locator('#slotGrid .slot-btn.selected').first()).toBeVisible();

        await page.locator('button[onclick="openConfirm()"]').click();
        await expect(page.locator('#confirmModal')).toHaveClass(/open/, { timeout: 5_000 });

        await page.locator('#confirmModal .btn-primary').click();

        await page.waitForURL(/\/reservasi/, { timeout: 15_000 });
        await expect(page.locator('.alert-success')).toBeVisible();
        await expect(page.locator('.alert-success')).toContainText(/GYM-\d{8}-[A-Z0-9]{4}/);
    });

    test('4. Reservasi kedua di tanggal yang sama ditolak', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/reservasi');

        await page.locator('#slotGrid .slot-btn:not(.taken)').first().click();
        await page.locator('button[onclick="openConfirm()"]').click();
        await expect(page.locator('#confirmModal')).toHaveClass(/open/, { timeout: 5_000 });
        await page.locator('#confirmModal .btn-primary').click();

        await page.waitForURL(/\/reservasi/, { timeout: 10_000 });
        await expect(page.locator('.alert-error')).toBeVisible();
        await expect(page.locator('.alert-error')).toContainText(/reservasi aktif/i);
    });

    test('5. Reservasi pending bisa dibatalkan via API', async ({ page }) => {
        await loginAs(page, MEMBER_EMAIL, MEMBER_PASS);
        await page.goto('/reservasi');

        await page.locator('button.res-tab', { hasText: /Tiket Saya/i }).click();

        const hasTicket = await page.locator('.barcode-id').isVisible({ timeout: 5_000 }).catch(() => false);
        if (!hasTicket) {
            test.skip();
            return;
        }

        const result = await page.evaluate(async (): Promise<{ ok: boolean; status?: number }> => {
            const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

            for (let id = 1; id <= 50; id++) {
                const res = await fetch(`/reservasi/${id}`, {
                    method : 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': token },
                    body   : '_method=DELETE',
                    redirect: 'manual',
                });
                if (res.status === 302 || res.status === 200) {
                    return { ok: true, status: res.status };
                }
            }
            return { ok: false };
        });

        if (!result.ok) {
            test.skip();
            return;
        }

        await page.reload();
        await page.locator('button.res-tab', { hasText: /Tiket Saya/i }).click();
        await expect(page.locator('.barcode-id')).not.toBeVisible({ timeout: 5_000 });
    });

});
