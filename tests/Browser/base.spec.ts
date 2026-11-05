import { test, expect } from '@playwright/test';
import documents from '../fixtures/documents.json' with { type: 'json' };

test('Laravel bajo /documentacion/: índice, 21 rutas, recursos y retorno', async ({ page, request }) => {
  const base = '/documentacion';
  await page.goto(base + '/');
  await expect(page.locator('.index-list a')).toHaveCount(21);
  for (const doc of documents) {
    const path = `${base}/docs/${doc.id}/`;
    expect((await request.get(path)).status()).toBe(200);
    await expect(page.locator('.index-list a[href="' + path + '"]')).toHaveCount(1);
  }
  for (const path of ['/assets/logo.webp', '/build/bootstrap.min.css', '/build/corporate.css', '/build/documentation.js', '/assets/fonts/inter-latin-400.woff2']) expect((await request.get(base + path)).ok()).toBeTruthy();
  await page.goto(base + '/docs/authority-reporting/pre-check-in/');
  await expect(page.locator('.breadcrumbs a').last()).toHaveAttribute('href', base + '/docs/authority-reporting/');
  await page.locator('.brand-section').click();
  await expect(page).toHaveURL(base + '/');
  expect((await page.goto(base + '/inexistente/'))?.status()).toBe(404);
  await expect(page.locator('.primary-link')).toHaveAttribute('href', base + '/');
});

test('Laravel bajo /documentacion/ sin JavaScript conserva los seis hijos', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto('http://127.0.0.1:8081/documentacion/');
  await expect(page.locator('.nav-list a')).toHaveCount(21);
  await expect(page.locator('#authority-children')).toBeVisible();
  await page.locator('#authority-children a').last().click();
  await expect(page.locator('h1')).toHaveText('Bi Off-line Group Check-in');
  await page.locator('.brand-section').click();
  await expect(page.locator('h1')).toHaveText('Documentación de Binding Identity');
  await context.close();
});
