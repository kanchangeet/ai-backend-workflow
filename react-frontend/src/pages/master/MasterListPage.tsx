import React, { useState } from 'react'
import { Link } from 'react-router-dom'
import { Plus, Search, Pencil, Trash2 } from 'lucide-react'
import { useMasterList, useDeleteMaster } from '@/hooks/useMaster'
import { MasterItem } from '@/api/master'
import { Button, Table, Badge, Input, Pagination, ConfirmModal } from '@/components/ui'

export function MasterListPage() {
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [deleteId, setDeleteId] = useState<number | null>(null)

  const { data, isLoading } = useMasterList({ page, per_page: 10, search })
  const deleteMutation = useDeleteMaster()

  const columns: { key: string; header: string; className?: string; render?: (row: Record<string, unknown>) => React.ReactNode }[] = [
    { key: 'name', header: 'Name' },
    { key: 'code', header: 'Code', className: 'font-mono text-xs' },
    { key: 'description', header: 'Description', className: 'max-w-xs truncate' },
    {
      key: 'status',
      header: 'Status',
      render: (row) => {
        const item = row as unknown as MasterItem
        return (
          <Badge variant={item.status === 'active' ? 'success' : 'default'} dot>
            {item.status === 'active' ? 'Active' : 'Inactive'}
          </Badge>
        )
      },
    },
    {
      key: 'actions',
      header: '',
      className: 'w-24',
      render: (row) => {
        const item = row as unknown as MasterItem
        return (
          <div className="flex items-center gap-1">
            <Link to={`/category/${item.id}/edit`}>
              <Button variant="ghost" size="xs" leftIcon={<Pencil size={13} />} data-testid={`edit-${item.id}`}>
                Edit
              </Button>
            </Link>
            <Button
              variant="ghost"
              size="xs"
              leftIcon={<Trash2 size={13} />}
              className="text-red-500 hover:text-red-700 hover:bg-red-50"
              onClick={() => setDeleteId(item.id)}
              data-testid={`delete-${item.id}`}
            >
              Delete
            </Button>
          </div>
        )
      },
    },
  ]

  return (
    <div>
      <div className="page-header">
        <div>
          <h1 className="page-title">Category</h1>
          <p className="text-sm text-slate-500 mt-1">Manage categories</p>
        </div>
        <Link to="/category/create">
          <Button leftIcon={<Plus size={16} />} data-testid="create-master-btn">
            New Record
          </Button>
        </Link>
      </div>

      <div className="card">
        {/* Filters */}
        <div className="p-4 border-b border-slate-200">
          <div className="max-w-xs">
            <Input
              placeholder="Search by name or code..."
              leftAddon={<Search size={15} />}
              value={search}
              onChange={(e) => {
                setSearch(e.target.value)
                setPage(1)
              }}
              data-testid="search-input"
            />
          </div>
        </div>

        {/* Table */}
        <div className="p-4">
          <Table
            columns={columns}
            data={(data?.data ?? []) as unknown as Record<string, unknown>[]}
            keyField="id"
            loading={isLoading}
            emptyMessage="No categories found. Create your first one."
          />

          {data && data.meta.last_page > 1 && (
            <Pagination
              page={data.meta.current_page}
              lastPage={data.meta.last_page}
              total={data.meta.total}
              perPage={data.meta.per_page}
              onPageChange={setPage}
            />
          )}
        </div>
      </div>

      <ConfirmModal
        open={deleteId !== null}
        onClose={() => setDeleteId(null)}
        onConfirm={() => {
          if (deleteId !== null) {
            deleteMutation.mutate(deleteId, {
              onSuccess: () => setDeleteId(null),
            })
          }
        }}
        title="Delete Category"
        message="Are you sure you want to delete this category? This action cannot be undone."
        confirmLabel="Delete"
        loading={deleteMutation.isPending}
      />
    </div>
  )
}
