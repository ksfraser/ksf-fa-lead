import { test, expect } from '@playwright/test';

test.describe('ksf_FA_Lead Lead Module', () => {
  test('should load lead list page', async ({ page }) => {
    await page.goto('/modules/ksf_FA_Lead/pages/leads.php');
    await expect(page.locator('h1, h2')).toContainText(/Lead/i);
  });
});
