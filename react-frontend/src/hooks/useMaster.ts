import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useNavigate } from 'react-router-dom'
import { masterApi, MasterListParams, MasterPayload } from '@/api/master'

const MASTER_KEY = 'master'

export function useMasterList(params?: MasterListParams) {
  return useQuery({
    queryKey: [MASTER_KEY, 'list', params],
    queryFn: () => masterApi.list(params),
  })
}

export function useMasterItem(id: number) {
  return useQuery({
    queryKey: [MASTER_KEY, id],
    queryFn: () => masterApi.get(id),
    enabled: !!id,
  })
}

export function useCreateMaster() {
  const queryClient = useQueryClient()
  const navigate = useNavigate()

  return useMutation({
    mutationFn: (payload: MasterPayload) => masterApi.create(payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [MASTER_KEY, 'list'] })
      navigate('/category')
    },
  })
}

export function useUpdateMaster(id: number) {
  const queryClient = useQueryClient()
  const navigate = useNavigate()

  return useMutation({
    mutationFn: (payload: Partial<MasterPayload>) => masterApi.update(id, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [MASTER_KEY, 'list'] })
      queryClient.invalidateQueries({ queryKey: [MASTER_KEY, id] })
      navigate('/category')
    },
  })
}

export function useDeleteMaster() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (id: number) => masterApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [MASTER_KEY, 'list'] })
    },
  })
}
