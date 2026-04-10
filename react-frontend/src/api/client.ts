import axios from 'axios'
import { useAuthStore } from '@/store/authStore'

// Empty string → relative URLs (/api/...) — nginx in Docker proxies to BFF.
// Set VITE_BFF_URL in .env for local dev (e.g. http://localhost:9080).
const BASE_URL = import.meta.env.VITE_BFF_URL || ''

export const apiClient = axios.create({
  baseURL: `${BASE_URL}/api`,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Attach JWT token to every request
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Handle 401 - redirect to login
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      useAuthStore.getState().clearAuth()
      window.location.href = '/login'
    }
    return Promise.reject(error)
  },
)

export default apiClient
