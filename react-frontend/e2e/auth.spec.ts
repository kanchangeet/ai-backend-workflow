import { test, expect } from '@playwright/test'
import { loginAs, logout, TEST_USER } from './helpers/auth'

test.describe('Authentication', () => {
  test('login page renders correctly', async ({ page }) => {
    await page.goto('/login')

    await expect(page.getByTestId('login-form')).toBeVisible()
    await expect(page.getByTestId('email-input')).toBeVisible()
    await expect(page.getByTestId('password-input')).toBeVisible()
    await expect(page.getByTestId('login-submit')).toBeVisible()
    await expect(page.getByRole('link', { name: /create one/i })).toBeVisible()
  })

  test('shows validation errors on empty submit', async ({ page }) => {
    await page.goto('/login')
    await page.getByTestId('login-submit').click()

    await expect(page.getByText('Email is required')).toBeVisible()
    await expect(page.getByText('Password is required')).toBeVisible()
  })

  test('shows error for invalid email format', async ({ page }) => {
    await page.goto('/login')
    await page.getByTestId('email-input').fill('not-an-email')
    await page.getByTestId('login-submit').click()

    await expect(page.getByText('Invalid email')).toBeVisible()
  })

  test('navigates to register page', async ({ page }) => {
    await page.goto('/login')
    await page.getByRole('link', { name: /create one/i }).click()
    await page.waitForURL('/register')

    await expect(page.getByTestId('register-form')).toBeVisible()
  })

  test('register page shows all fields', async ({ page }) => {
    await page.goto('/register')

    await expect(page.getByTestId('name-input')).toBeVisible()
    await expect(page.getByTestId('email-input')).toBeVisible()
    await expect(page.getByTestId('password-input')).toBeVisible()
    await expect(page.getByTestId('password-confirm-input')).toBeVisible()
    await expect(page.getByTestId('register-submit')).toBeVisible()
  })

  test('register validates password mismatch', async ({ page }) => {
    await page.goto('/register')
    await page.getByTestId('name-input').fill('Test User')
    await page.getByTestId('email-input').fill('new@example.com')
    await page.getByTestId('password-input').fill('password123')
    await page.getByTestId('password-confirm-input').fill('different123')
    await page.getByTestId('register-submit').click()

    await expect(page.getByText('Passwords do not match')).toBeVisible()
  })

  test('successful login redirects to master', async ({ page }) => {
    await loginAs(page)
    await expect(page).toHaveURL('/master')
  })

  test('logout clears session and redirects to login', async ({ page }) => {
    await loginAs(page)
    await logout(page)
    await expect(page).toHaveURL('/login')
  })

  test('unauthenticated user is redirected to login', async ({ page }) => {
    await page.goto('/master')
    await page.waitForURL('/login')
    await expect(page.getByTestId('login-form')).toBeVisible()
  })

  test('authenticated user is redirected away from login', async ({ page }) => {
    await loginAs(page)
    await page.goto('/login')
    await page.waitForURL('/master')
  })
})
