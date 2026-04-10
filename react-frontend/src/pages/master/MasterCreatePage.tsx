import { useCreateMaster } from '@/hooks/useMaster'
import { MasterPayload } from '@/api/master'
import { MasterForm } from '@/components/master/MasterForm'

export function MasterCreatePage() {
  const createMutation = useCreateMaster()

  const handleSubmit = (data: MasterPayload) => {
    createMutation.mutate(data)
  }

  return (
    <MasterForm
      title="Create Category"
      onSubmit={handleSubmit}
      loading={createMutation.isPending}
    />
  )
}
