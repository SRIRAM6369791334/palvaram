import { test, expect } from '@playwright/test';

test('has title', async ({ page }) => {
  await page.goto('http://localhost:5173/');
  await expect(page).toHaveTitle(/Laravel/);
});

test('check layout on mobile', async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 667 });
  await page.goto('http://localhost:5173/');
  // Check if it's responsive
  await page.screenshot({ path: 'mobile-layout.png' });
});

test('check layout on desktop', async ({ page }) => {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.goto('http://localhost:5173/');
  // Check if it's responsive
  await page.screenshot({ path: 'desktop-layout.png' });
});
