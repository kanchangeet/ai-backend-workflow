import { Page } from '@playwright/test'

export const TEST_USER = {
  email: 'test2@example.com',
  password: 'password123',
  name: 'Test User2',
}

export async function loginAs(page: Page, email = TEST_USER.email, password = TEST_USER.password) {
  await page.goto('/login')
  await page.getByTestId('email-input').fill(email)
  await page.getByTestId('password-input').fill(password)
  await page.getByTestId('login-submit').click()
  await page.waitForURL('/master')
}

export async function logout(page: Page) {
  await page.getByRole('button', { name: /logout/i }).click()
  await page.waitForURL('/login')
}
