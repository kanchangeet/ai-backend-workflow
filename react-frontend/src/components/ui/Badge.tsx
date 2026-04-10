type BadgeVariant = 'default' | 'primary' | 'success' | 'warning' | 'danger' | 'info'

interface BadgeProps {
  variant?: BadgeVariant
  children: React.ReactNode
  dot?: boolean
}

const variantClasses: Record<BadgeVariant, string> = {
  default: 'bg-slate-100 text-slate-700',
  primary: 'bg-primary-100 text-primary-700',
  success: 'bg-green-100 text-green-700',
  warning: 'bg-amber-100 text-amber-700',
  danger: 'bg-red-100 text-red-700',
  info: 'bg-blue-100 text-blue-700',
}

const dotClasses: Record<BadgeVariant, string> = {
  default: 'bg-slate-500',
  primary: 'bg-primary-500',
  success: 'bg-green-500',
  warning: 'bg-amber-500',
  danger: 'bg-red-500',
  info: 'bg-blue-500',
}

export function Badge({ variant = 'default', children, dot = false }: BadgeProps) {
  return (
    <span
      className={[
        'inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-0.5 rounded-full',
        variantClasses[variant],
      ].join(' ')}
    >
      {dot && (
        <span className={['w-1.5 h-1.5 rounded-full', dotClasses[variant]].join(' ')} />
      )}
      {children}
    </span>
  )
}
