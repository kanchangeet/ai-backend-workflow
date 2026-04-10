import { AlertCircle, CheckCircle2, Info, XCircle, X } from 'lucide-react'
import { ReactNode, useState } from 'react'

type AlertVariant = 'info' | 'success' | 'warning' | 'error'

interface AlertProps {
  variant?: AlertVariant
  title?: string
  children: ReactNode
  dismissible?: boolean
  className?: string
}

const config: Record<AlertVariant, { icon: typeof Info; classes: string }> = {
  info: { icon: Info, classes: 'bg-blue-50 text-blue-800 border-blue-200' },
  success: { icon: CheckCircle2, classes: 'bg-green-50 text-green-800 border-green-200' },
  warning: { icon: AlertCircle, classes: 'bg-amber-50 text-amber-800 border-amber-200' },
  error: { icon: XCircle, classes: 'bg-red-50 text-red-800 border-red-200' },
}

export function Alert({ variant = 'info', title, children, dismissible = false, className }: AlertProps) {
  const [dismissed, setDismissed] = useState(false)
  if (dismissed) return null

  const { icon: Icon, classes } = config[variant]

  return (
    <div className={['flex gap-3 p-4 rounded-xl border', classes, className ?? ''].join(' ')} role="alert">
      <Icon size={18} className="shrink-0 mt-0.5" />
      <div className="flex-1 text-sm">
        {title && <p className="font-semibold mb-1">{title}</p>}
        <div>{children}</div>
      </div>
      {dismissible && (
        <button onClick={() => setDismissed(true)} className="shrink-0 opacity-60 hover:opacity-100">
          <X size={16} />
        </button>
      )}
    </div>
  )
}
