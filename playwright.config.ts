import { defineConfig, devices } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import process from 'node:process';

mkdirSync('verification', { recursive: true });
const prefix = process.env.DOCS_BASE_CHECK === '1';
const origin = prefix ? 'http://127.0.0.1:8081' : 'http://127.0.0.1:8080';
const channel = process.env.PLAYWRIGHT_CHANNEL ?? (process.env.CI ? undefined : 'chrome');

export default defineConfig({
  testDir: './tests/Browser',
  ...(prefix ? { testMatch: '**/base.spec.ts' } : { testIgnore: '**/base.spec.ts' }),
  outputDir: prefix ? 'test-results/base' : 'test-results/root',
  timeout: 60000, fullyParallel: true, workers: 2,
  reporter: [['list'], ['json', { outputFile: prefix ? 'verification/base-results.json' : 'verification/results.json' }]],
  use: { baseURL: origin, trace: 'retain-on-failure' },
  projects: [{ name: channel ?? 'chromium', use: { ...devices['Desktop Chrome'], ...(channel ? { channel } : {}) } }],
  webServer: prefix ? undefined : {
    command: 'node scripts/serve.mjs', url: origin, reuseExistingServer: !process.env.CI,
  },
});
