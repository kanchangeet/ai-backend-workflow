import { Bell, LogOut, User } from 'lucide-react'
import { useAuthStore } from '@/store/authStore'
import { useLogout } from '@/hooks/useAuth'
import { Button } from '@/components/ui'

interface HeaderProps {
  title?: string
}

export function Header({ title }: HeaderProps) {
  const { user } = useAuthStore()
  const logout = useLogout()

  return (
    <header className="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0">
      <div>
        {title && <h2 className="font-semibold text-slate-900">{title}</h2>}
      </div>

      <div className="flex items-center gap-3">
        <button className="relative text-slate-400 hover:text-slate-600 transition-colors p-1.5 rounded-lg hover:bg-slate-100">
          <Bell size={18} />
          <span className="absolute top-1 right-1 w-1.5 h-1.5 bg-red-500 rounded-full" />
        </button>

        <div className="flex items-center gap-2 pl-3 border-l border-slate-200">
          <div className="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
            <User size={16} className="text-primary-600" />
          </div>
          <div className="hidden sm:block">
            <p className="text-sm font-medium text-slate-900 leading-none">{user?.name}</p>
            <p className="text-xs text-slate-500 mt-0.5">{user?.email}</p>
          </div>
        </div>

        <Button
          variant="ghost"
          size="sm"
          leftIcon={<LogOut size={15} />}
          onClick={() => logout.mutate()}
          loading={logout.isPending}
        >
          Logout
        </Button>
      </div>
    </header>
  )
}
