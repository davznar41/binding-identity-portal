import { test, expect, type Page } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';
import documents from '../fixtures/documents.json' with { type: 'json' };

const parent = '/docs/authority-reporting/';
const child = '/docs/authority-reporting/pre-check-in/';
const normal = '/docs/id-validate/';
const paths = documents.map(doc => `/docs/${doc.id}/`);
const checkAxe = async (page: Page) => {
  const result = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag2aaa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice']).analyze();
  test.info().annotations.push({ type: 'axe', description: JSON.stringify({ url: page.url(), version: result.testEngine.version, violations: result.violations.length, incomplete: result.incomplete.map(item => ({ id: item.id, targets: item.nodes.map(node => node.target) })) }) });
  expect(result.violations).toEqual([]);
};

for (const doc of documents) {
  test(`Laravel: contenido, ruta, recarga y axe de ${doc.id}`, async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', error => errors.push(error.message));
    const response = await page.goto(`/docs/${doc.id}/`);
    expect(response?.status()).toBe(200);
    await expect(page.locator('h1')).toHaveCount(1);
    await expect(page.locator('h1')).toHaveText(doc.title);
    await expect(page.locator('.prose')).toHaveText(doc.description);
    if ('example' in doc) await expect(page.locator('.example p')).toHaveText(doc.example!);
    else await expect(page.locator('.example')).toHaveCount(0);
    await expect(page.locator('[aria-current="page"]')).toHaveCount(1);
    await expect(page.locator('[aria-current="page"]')).toHaveAccessibleName(doc.title);
    await expect(page.locator('.breadcrumbs')).toHaveAccessibleName('Migas de pan');
    if (doc.id.includes('/')) await expect(page.locator('.breadcrumbs a').last()).toHaveAttribute('href', parent);
    await expect(page.locator('footer, .article-position, .article-meta, .nav-number, .documentation-icon')).toHaveCount(0);
    await page.reload();
    await expect(page.locator('h1')).toHaveText(doc.title);
    await checkAxe(page);
    expect(errors).toEqual([]);
  });
}

test('Laravel: índice 15+6, 21 enlaces originales y semántica', async ({ page, request }) => {
  await page.goto('/');
  await expect(page.locator('h1')).toHaveText('Documentación de Binding Identity');
  await expect(page.locator('.index-list > li')).toHaveCount(15);
  await expect(page.locator('.index-children a')).toHaveCount(6);
  expect(await page.locator('.index-list a').evaluateAll(links => links.map(a => a.getAttribute('href')))).toEqual(paths);
  expect((await page.locator('.nav-list a').allTextContents()).map(text => text.replace(/\s+/g, ' ').trim())).toEqual(documents.map(doc => doc.title));
  await expect(page.locator('.count, .nav-number, .sidebar-heading, .sidebar-note, .home-stats')).toHaveCount(0);
  for (const path of paths) expect((await request.get(path)).status()).toBe(200);
  await expect(page.locator('.breadcrumbs')).toHaveCount(0);
});

test('Laravel: cabecera nativa, logo no enlazado y retorno desde los 21 artículos', async ({ page }) => {
  for (const width of [1440, 390]) {
    await page.setViewportSize({ width, height: 900 });
    for (const path of paths) {
      await page.goto(path);
      const banner = page.getByRole('banner');
      await expect(banner.locator('a')).toHaveCount(1);
      await expect(banner.locator('a img')).toHaveCount(0);
      await expect(banner.locator('img')).toHaveAttribute('alt', 'Binding Software');
      const home = banner.getByRole('link', { name: 'Documentación', exact: true });
      await expect(home).toHaveAttribute('href', '/');
      await expect(home).not.toHaveAttribute('aria-label');
      await expect(home).not.toHaveAttribute('aria-current');
      await home.click();
      await expect(page).toHaveURL('/');
      await expect(page.locator('.index-list a')).toHaveCount(21);
    }
  }
});

test('Laravel: anterior y siguiente conservan exactamente el orden editorial', async ({ page }) => {
  for (let i = 0; i < documents.length; i++) {
    await page.goto(paths[i]);
    const pager = page.getByRole('navigation', { name: 'Artículos anterior y siguiente' });
    const hrefs = await pager.locator('a').evaluateAll(links => links.map(a => a.getAttribute('href')));
    expect(hrefs).toEqual([paths[i - 1], paths[i + 1]].filter(Boolean));
  }
});

test('Laravel: padre y desplegable independientes, Espacio, Enter y Shift+Tab', async ({ page }) => {
  await page.goto('/');
  const toggle = page.getByRole('button', { name: 'Subapartados de Bi Authority Reporting' });
  await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  await toggle.focus();
  await page.keyboard.press('Space');
  await expect(toggle).toHaveAttribute('aria-expanded', 'true');
  await page.keyboard.press('Tab');
  await expect(page.locator('#authority-children a').first()).toBeFocused();
  await page.keyboard.press('Shift+Tab');
  await expect(toggle).toBeFocused();
  await page.keyboard.press('Enter');
  await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  await page.locator('.nav-row > a', { hasText: 'Bi Authority Reporting' }).click();
  await expect(page).toHaveURL(parent);
  await expect(page.locator('.related a')).toHaveCount(6);
  await toggle.click();
  await checkAxe(page);
  await page.locator('#authority-children a').first().click();
  await expect(page).toHaveURL(child);
  await expect(toggle).toHaveAttribute('aria-expanded', 'true');
});

test('Laravel: salto, foco visible, orden y Enter en cabecera', async ({ page }) => {
  for (const width of [1440, 390]) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto(normal);
    await page.keyboard.press('Tab');
    await expect(page.locator('.skip-link')).toBeFocused();
    await page.keyboard.press('Enter');
    await expect(page.locator('main')).toBeFocused();
    await expect(page.locator('main')).toHaveCSS('outline-width', '3px');
    await page.goto(child);
    await page.keyboard.press('Tab');
    await page.keyboard.press('Tab');
    const link = page.locator('.brand-section');
    await expect(link).toBeFocused();
    await expect(link).toHaveCSS('outline-width', '3px');
    await expect(link).toHaveCSS('text-decoration-line', 'underline');
    const box = await link.boundingBox();
    expect(box!.width).toBeGreaterThanOrEqual(44);
    expect(box!.height).toBeGreaterThanOrEqual(44);
    expect(box!.x).toBeGreaterThanOrEqual(3);
    expect(box!.x + box!.width).toBeLessThanOrEqual(width - 3);
    await page.keyboard.press('Enter');
    await expect(page).toHaveURL('/');
  }
});

test('Laravel: menú móvil no modal y Escape restaura el foco', async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto(child);
  const toggle = page.getByRole('button', { name: 'Explorar documentación' });
  await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  await toggle.press('Space');
  await page.keyboard.press('Tab');
  await expect(page.locator('.nav-list a').first()).toBeFocused();
  await page.keyboard.press('Escape');
  await expect(toggle).toBeFocused();
  await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  await toggle.press('Enter');
  await page.locator('#authority-children a').nth(1).click();
  await expect(page).toHaveURL('/docs/authority-reporting/online-check-in/');
  await page.locator('.mobile-toggle').click();
  await checkAxe(page);
  await page.screenshot({ path: 'verification/laravel-menu-movil.png', fullPage: true });
});

test('Laravel: el cambio de breakpoint conserva un foco utilizable', async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 900 });
  await page.goto(normal);
  await page.locator('.mobile-toggle').focus();
  await page.setViewportSize({ width: 1440, height: 900 });
  await expect(page.locator('.nav-list a').first()).toBeFocused();
  await page.setViewportSize({ width: 390, height: 900 });
  await expect(page.locator('#documentation-navigation')).toBeVisible();
  await expect(page.locator('.nav-list a').first()).toBeFocused();
});

for (const width of [320, 375, 390, 430, 768, 1024, 1280, 1440]) {
  test(`Laravel: reflujo sin solapamiento ni títulos truncados a ${width}px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 1000 });
    for (const path of ['/', normal, parent, child]) {
      await page.goto(path);
      if (width < 960) await page.locator('.mobile-toggle').click();
      expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBeTruthy();
      for (const link of await page.locator('.nav-list a').all()) {
        if (await link.isVisible()) expect(await link.evaluate(a => a.scrollHeight <= a.clientHeight + 1 && a.scrollWidth <= a.clientWidth + 1)).toBeTruthy();
      }
      const rectangles = await page.locator('.corporate-logo, .brand-section').evaluateAll(els => els.map(el => { const r = el.getBoundingClientRect(); return { left: r.left, right: r.right, top: r.top, bottom: r.bottom }; }));
      expect(rectangles[0].right <= rectangles[1].left || rectangles[0].bottom <= rectangles[1].top).toBeTruthy();
    }
  });
}

test('Laravel: todos los enlaces siguen disponibles sin JavaScript', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto('http://127.0.0.1:8080/');
  await expect(page.locator('.nav-list a')).toHaveCount(21);
  await expect(page.locator('#authority-children')).toBeVisible();
  await expect(page.locator('.mobile-toggle')).toBeHidden();
  await page.locator('#authority-children a').first().click();
  await expect(page.locator('h1')).toHaveText('Bi Pre Check-in');
  await page.locator('.brand-section').click();
  await expect(page.locator('h1')).toHaveText('Documentación de Binding Identity');
  await context.close();
});

test('Laravel: Bootstrap 5.3, fuentes locales y ausencia de servicios externos', async ({ page, baseURL, request }) => {
  const external: string[] = [];
  page.on('request', req => { if (!req.url().startsWith(baseURL!)) external.push(req.url()); });
  await page.goto(normal);
  expect((await request.get('/build/manifest.json')).ok()).toBeTruthy();
  const manifest = await (await request.get('/build/manifest.json')).json();
  expect(manifest.bootstrap).toMatch(/^5\.3\./);
  await expect(page.locator('link[href="/build/bootstrap.min.css"]')).toHaveCount(1);
  const fonts = await page.evaluate(async () => {
    const loaded = await Promise.all([document.fonts.load('16px Inter'), document.fonts.load('700 32px Montserrat')]);
    return loaded.map(faces => faces.map(face => face.status));
  });
  expect(fonts).toEqual([['loaded'], ['loaded']]);
  await page.route('**/*.woff2', route => route.abort());
  await page.reload();
  await expect(page.locator('.prose')).toBeVisible();
  expect(external).toEqual([]);
});

test('Laravel: 404 real, útil y con navegación al índice', async ({ page }) => {
  expect((await page.goto('/pagina-inexistente/'))?.status()).toBe(404);
  await expect(page.locator('h1')).toHaveText('Página no encontrada');
  await expect(page.locator('footer')).toHaveCount(0);
  await checkAxe(page);
  await page.getByRole('link', { name: 'Volver al índice' }).click();
  await expect(page).toHaveURL('/');
});

test('Laravel: capturas nuevas de portada, artículo y padre en escritorio y móvil', async ({ page }) => {
  for (const width of [1440, 390]) {
    await page.setViewportSize({ width, height: 900 });
    for (const [name, path] of [['portada', '/'], ['id-validate', normal], ['apartado15', parent], ['hija', child]]) {
      await page.goto(path);
      await page.evaluate(() => document.fonts.ready);
      await page.screenshot({ path: `verification/laravel-${name}-${width}.png`, fullPage: true });
      if (name === 'portada') await checkAxe(page);
    }
    await page.goto(child);
    await page.keyboard.press('Tab');
    await page.keyboard.press('Tab');
    await page.locator('header.site-header').screenshot({ path: `verification/laravel-cabecera-foco-${width}.png` });
  }
});

test('Laravel: preferencias emuladas y ampliación de texto CSS', async ({ page }) => {
  await page.emulateMedia({ forcedColors: 'active', reducedMotion: 'reduce' });
  await page.goto(normal);
  await page.keyboard.press('Tab');
  await page.keyboard.press('Tab');
  await expect(page.locator('.brand-section')).toHaveCSS('outline-width', '3px');
  await page.emulateMedia({ forcedColors: 'none', reducedMotion: 'no-preference' });
  await page.addStyleTag({ content: 'html{font-size:200%!important}p{line-height:1.5!important;letter-spacing:.12em!important;word-spacing:.16em!important}' });
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBeTruthy();
});
