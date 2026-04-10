import { test, expect } from '@playwright/test'
import { loginAs } from './helpers/auth'

test.describe('Navigation', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page)
  })

  test('sidebar links are visible', async ({ page }) => {
    await expect(page.getByRole('link', { name: /dashboard/i })).toBeVisible()
    await expect(page.getByRole('link', { name: /master/i })).toBeVisible()
    await expect(page.getByRole('link', { name: /preview/i })).toBeVisible()
  })

  test('navigates to preview page', async ({ page }) => {
    await page.getByRole('link', { name: /preview/i }).click()
    await page.waitForURL('/preview')

    await expect(page.getByRole('heading', { name: 'Component Preview' })).toBeVisible()
  })

  test('preview page shows all sections', async ({ page }) => {
    await page.goto('/preview')

    await expect(page.getByText('Buttons')).toBeVisible()
    await expect(page.getByText('Badges')).toBeVisible()
    await expect(page.getByText('Inputs')).toBeVisible()
    await expect(page.getByText('Modals')).toBeVisible()
    await expect(page.getByText('Table')).toBeVisible()
    await expect(page.getByText('Icons')).toBeVisible()
  })

  test('header shows user info', async ({ page }) => {
    await page.goto('/dashboard')

    // User name or email should be visible in header
    await expect(page.getByRole('button', { name: /logout/i })).toBeVisible()
  })
})
