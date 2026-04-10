import { useQuery } from '@tanstack/react-query'
import { Database, Users } from 'lucide-react'
import { useAuthStore } from '@/store/authStore'
import { dashboardApi } from '@/api/dashboard'

interface StatCardProps {
  label: string
  value: string | number
  icon: typeof Database
  color: string
  loading?: boolean
}

function StatCard({ label, value, icon: Icon, color, loading }: StatCardProps) {
  return (
    <div className="card p-6">
      <div className="flex items-start justify-between">
        <div>
          <p className="text-sm text-slate-500">{label}</p>
          {loading ? (
            <div className="h-8 w-16 bg-slate-200 animate-pulse rounded mt-1" />
          ) : (
            <p className="text-2xl font-bold text-slate-900 mt-1">{value}</p>
          )}
        </div>
        <div className={`w-10 h-10 rounded-xl flex items-center justify-center ${color}`}>
          <Icon size={20} />
        </div>
      </div>
    </div>
  )
}

export function DashboardPage() {
  const { user } = useAuthStore()

  const { data, isLoading } = useQuery({
    queryKey: ['dashboard'],
    queryFn: dashboardApi.get,
  })

  return (
    <div>
      <div className="mb-6">
        <h1 className="page-title">Dashboard</h1>
        <p className="text-sm text-slate-500 mt-1">Welcome back, {user?.name}</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <StatCard
          label="Total Categories"
          value={data?.total_categories ?? 0}
          icon={Database}
          color="bg-primary-100 text-primary-600"
          loading={isLoading}
        />
        <StatCard
          label="Total Users"
          value={data?.total_users ?? 0}
          icon={Users}
          color="bg-green-100 text-green-600"
          loading={isLoading}
        />
      </div>

      <div className="card p-6">
        <h2 className="font-semibold text-slate-900 mb-2">Quick Links</h2>
        <p className="text-sm text-slate-500">
          Navigate to <a className="text-primary-600 font-medium hover:underline" href="/category">Category</a> to manage categories, or visit the{' '}
          <a className="text-primary-600 font-medium hover:underline" href="/preview">Preview</a> page to explore the design system.
        </p>
      </div>
    </div>
  )
}
