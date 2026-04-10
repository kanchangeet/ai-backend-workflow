import apiClient from './client'

export interface DashboardData {
  user: {
    id: number
    name: string
    email: string
  }
  total_categories: number
  total_users: number
}

export const dashboardApi = {
  get: async (): Promise<DashboardData> => {
    const { data } = await apiClient.get<DashboardData>('/dashboard')
    return data
  },
}
