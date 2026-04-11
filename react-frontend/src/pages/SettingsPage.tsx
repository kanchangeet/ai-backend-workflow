import { useAuthStore } from '@/store/authStore'
import { User, Mail, Calendar } from 'lucide-react'

export function SettingsPage() {
  const { user } = useAuthStore()

  return (
    <div>
      <div className="mb-6">
        <h1 className="page-title">Settings</h1>
        <p className="text-sm text-slate-500 mt-1">Manage your account preferences</p>
      </div>

      <div className="grid gap-6">
        {/* User Information Card */}
        <div className="card p-6">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-10 h-10 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center">
              <User size={20} />
            </div>
            <h2 className="font-semibold text-slate-900">Profile Information</h2>
          </div>
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <User size={16} className="text-slate-400" />
              <span className="text-sm text-slate-600">Name:</span>
              <span className="text-sm font-medium text-slate-900">{user?.name || 'Not set'}</span>
            </div>
            <div className="flex items-center gap-3">
              <Mail size={16} className="text-slate-400" />
              <span className="text-sm text-slate-600">Email:</span>
              <span className="text-sm font-medium text-slate-900">{user?.email || 'Not set'}</span>
            </div>
            <div className="flex items-center gap-3">
              <Calendar size={16} className="text-slate-400" />
              <span className="text-sm text-slate-600">User ID:</span>
              <span className="text-sm font-medium text-slate-900">#{user?.id || 'Unknown'}</span>
            </div>
          </div>
        </div>

        {/* Application Settings Card */}
        <div className="card p-6">
          <h2 className="font-semibold text-slate-900 mb-4">Application Settings</h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <label className="text-sm font-medium text-slate-900">Theme</label>
                <p className="text-sm text-slate-500">Choose your preferred theme</p>
              </div>
              <select className="text-sm border border-slate-300 rounded-lg px-3 py-2 bg-white">
                <option value="light">Light</option>
                <option value="dark" disabled>Dark (Coming Soon)</option>
              </select>
            </div>
            <div className="flex items-center justify-between">
              <div>
                <label className="text-sm font-medium text-slate-900">Language</label>
                <p className="text-sm text-slate-500">Select your language preference</p>
              </div>
              <select className="text-sm border border-slate-300 rounded-lg px-3 py-2 bg-white">
                <option value="en">English</option>
                <option value="es" disabled>Spanish (Coming Soon)</option>
                <option value="fr" disabled>French (Coming Soon)</option>
              </select>
            </div>
          </div>
        </div>

        {/* About Card */}
        <div className="card p-6">
          <h2 className="font-semibold text-slate-900 mb-2">About Claude Demo App</h2>
          <p className="text-sm text-slate-500">
            This is a demonstration application showcasing a modern React frontend with a Laravel backend.
            Built with TypeScript, Tailwind CSS, and following clean architecture principles.
          </p>
        </div>
      </div>
    </div>
  )
}