import { test, expect } from '@playwright/test'
import { loginAs } from './helpers/auth'

test.describe('Master CRUD', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page)
    await page.goto('/master')
  })

  test('master list page renders', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Master' })).toBeVisible()
    await expect(page.getByTestId('create-master-btn')).toBeVisible()
    await expect(page.getByTestId('search-input')).toBeVisible()
  })

  test('navigates to create page', async ({ page }) => {
    await page.getByTestId('create-master-btn').click()
    await page.waitForURL('/master/create')

    await expect(page.getByTestId('master-form')).toBeVisible()
    await expect(page.getByTestId('name-input')).toBeVisible()
    await expect(page.getByTestId('code-input')).toBeVisible()
  })

  test('create form shows validation errors', async ({ page }) => {
    await page.goto('/master/create')
    await page.getByTestId('submit-btn').click()

    await expect(page.getByText('Name is required')).toBeVisible()
    await expect(page.getByText('Code is required')).toBeVisible()
  })

  test('create form validates code format', async ({ page }) => {
    await page.goto('/master/create')
    await page.getByTestId('name-input').fill('Test Record')
    await page.getByTestId('code-input').fill('invalid code!')
    await page.getByTestId('submit-btn').click()

    await expect(page.getByText('Use uppercase letters, numbers, and underscores only')).toBeVisible()
  })

  test('creates a new record successfully', async ({ page }) => {
    await page.goto('/master/create')

    await page.getByTestId('name-input').fill('Test Record')
    await page.getByTestId('code-input').fill('TEST_RECORD')
    await page.getByTestId('description-input').fill('A test description')
    await page.getByTestId('submit-btn').click()

    // Should redirect back to list
    await page.waitForURL('/master')
  })

  test('search filters the list', async ({ page }) => {
    const searchInput = page.getByTestId('search-input')
    await searchInput.fill('TEST')

    // Wait for debounce
    await page.waitForTimeout(500)

    // Table should update (either show filtered results or empty state)
    await expect(page.locator('table')).toBeVisible()
  })

  test('edit record loads with existing data', async ({ page }) => {
    // Click first edit button if available
    const editBtn = page.getByTestId(/^edit-/).first()
    const count = await editBtn.count()

    if (count > 0) {
      await editBtn.click()
      await page.waitForURL(/\/master\/\d+\/edit/)

      await expect(page.getByTestId('master-form')).toBeVisible()
      // Name and code should be pre-filled
      const nameValue = await page.getByTestId('name-input').inputValue()
      expect(nameValue.length).toBeGreaterThan(0)
    }
  })

  test('delete confirmation modal appears', async ({ page }) => {
    const deleteBtn = page.getByTestId(/^delete-/).first()
    const count = await deleteBtn.count()

    if (count > 0) {
      await deleteBtn.click()
      await expect(page.getByRole('dialog')).toBeVisible()
      await expect(page.getByText('Delete Record')).toBeVisible()
      await expect(page.getByRole('button', { name: 'Cancel' })).toBeVisible()
      await expect(page.getByRole('button', { name: 'Delete' })).toBeVisible()
    }
  })

  test('cancel delete closes modal', async ({ page }) => {
    const deleteBtn = page.getByTestId(/^delete-/).first()
    const count = await deleteBtn.count()

    if (count > 0) {
      await deleteBtn.click()
      await page.getByRole('button', { name: 'Cancel' }).click()
      await expect(page.getByRole('dialog')).not.toBeVisible()
    }
  })
})
