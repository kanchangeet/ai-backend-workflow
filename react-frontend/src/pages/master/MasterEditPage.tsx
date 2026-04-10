import { useParams, Navigate } from 'react-router-dom'
import { useMasterItem, useUpdateMaster } from '@/hooks/useMaster'
import { MasterPayload } from '@/api/master'
import { MasterForm } from '@/components/master/MasterForm'
import { LoadingSpinner } from '@/components/ui'

export function MasterEditPage() {
  const { id } = useParams<{ id: string }>()
  const numericId = Number(id)

  const { data, isLoading, isError } = useMasterItem(numericId)
  const updateMutation = useUpdateMaster(numericId)

  if (isLoading) return <LoadingSpinner />
  if (isError || !data) return <Navigate to="/category" replace />

  const handleSubmit = (payload: MasterPayload) => {
    updateMutation.mutate(payload)
  }

  return (
    <MasterForm
      title="Edit Category"
      defaultValues={data}
      onSubmit={handleSubmit}
      loading={updateMutation.isPending}
    />
  )
}
