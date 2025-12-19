import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './e2e',
  outputDir: 'test-results/',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: 1,
  reporter: [
    ['html', { open: 'never' }],
    ['junit', { outputFile: 'test-results/results.xml' }],
    ['list']
  ],
  use: {
    baseURL: 'http://localhost/venta/',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'on',
  },
  projects: [
    {
      name: 'brave',
      use: {
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost/venta/',
        executablePath: '/Applications/Brave Browser.app/Contents/MacOS/Brave Browser'
      }
    },
    {
      name: 'modules',
      testDir: './e2e',
      testIgnore: 'e2e/debug/*.spec.js',
      use: {
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost/venta/',
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        video: 'on',
      }
    }
  ],
});
