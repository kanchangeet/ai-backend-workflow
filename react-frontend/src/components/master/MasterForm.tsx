import { useForm } from 'react-hook-form'
import { Link } from 'react-router-dom'
import { ArrowLeft } from 'lucide-react'
import { MasterPayload, MasterItem } from '@/api/master'
import { Button, Input, Select, Textarea } from '@/components/ui'

interface MasterFormProps {
  defaultValues?: Partial<MasterItem>
  onSubmit: (data: MasterPayload) => void
  loading: boolean
  title: string
}

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
]

export function MasterForm({ defaultValues, onSubmit, loading, title }: MasterFormProps) {
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<MasterPayload>({
    defaultValues: {
      name: defaultValues?.name ?? '',
      code: defaultValues?.code ?? '',
      description: defaultValues?.description ?? '',
      status: defaultValues?.status ?? 'active',
    },
  })

  return (
    <div>
      <div className="page-header">
        <div className="flex items-center gap-3">
          <Link to="/category">
            <Button variant="ghost" size="sm" leftIcon={<ArrowLeft size={15} />}>
              Back
            </Button>
          </Link>
          <h1 className="page-title">{title}</h1>
        </div>
      </div>

      <div className="card max-w-2xl">
        <div className="p-6">
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-5" data-testid="master-form">
            <Input
              label="Name"
              placeholder="Record name"
              error={errors.name?.message}
              required
              data-testid="name-input"
              {...register('name', { required: 'Name is required' })}
            />

            <Input
              label="Code"
              placeholder="UNIQUE_CODE"
              error={errors.code?.message}
              hint="Unique identifier (letters, numbers, underscores only)"
              required
              data-testid="code-input"
              {...register('code', {
                required: 'Code is required',
                pattern: {
                  value: /^[A-Za-z0-9_]+$/,
                  message: 'Use letters, numbers, and underscores only',
                },
              })}
            />

            <Textarea
              label="Description"
              placeholder="Optional description..."
              error={errors.description?.message}
              data-testid="description-input"
              {...register('description')}
            />

            <Select
              label="Status"
              options={statusOptions}
              error={errors.status?.message}
              required
              data-testid="status-select"
              {...register('status', { required: 'Status is required' })}
            />

            <div className="flex items-center gap-3 pt-2">
              <Button type="submit" loading={loading} data-testid="submit-btn">
                Save Record
              </Button>
              <Link to="/category">
                <Button variant="outline" type="button">
                  Cancel
                </Button>
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}
