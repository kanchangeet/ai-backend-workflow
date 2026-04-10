import apiClient from './client'

export interface MasterItem {
  id: number
  name: string
  code: string
  description: string
  status: 'active' | 'inactive'
  created_at: string
  updated_at: string
}

export interface MasterListResponse {
  data: MasterItem[]
  meta: {
    total: number
    per_page: number
    current_page: number
    last_page: number
  }
}

export interface MasterPayload {
  name: string
  code: string
  description: string
  status: 'active' | 'inactive'
}

export interface MasterListParams {
  page?: number
  per_page?: number
  search?: string
  status?: 'active' | 'inactive'
}

export const masterApi = {
  list: async (params?: MasterListParams): Promise<MasterListResponse> => {
    const { data } = await apiClient.get<MasterListResponse>('/master', { params })
    return data
  },

  get: async (id: number): Promise<MasterItem> => {
    const { data } = await apiClient.get<{ data: MasterItem }>(`/master/${id}`)
    return data.data
  },

  create: async (payload: MasterPayload): Promise<MasterItem> => {
    const { data } = await apiClient.post<MasterItem>('/master', payload)
    return data
  },

  update: async (id: number, payload: Partial<MasterPayload>): Promise<MasterItem> => {
    const { data } = await apiClient.put<MasterItem>(`/master/${id}`, payload)
    return data
  },

  delete: async (id: number): Promise<void> => {
    await apiClient.delete(`/master/${id}`)
  },
}
