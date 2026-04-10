import { ButtonHTMLAttributes, forwardRef } from 'react'
import { Loader2 } from 'lucide-react'

type Variant = 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger' | 'success'
type Size = 'xs' | 'sm' | 'md' | 'lg'

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant
  size?: Size
  loading?: boolean
  leftIcon?: React.ReactNode
  rightIcon?: React.ReactNode
}

const variantClasses: Record<Variant, string> = {
  primary:
    'bg-primary-500 text-white hover:bg-primary-600 focus:ring-primary-500 disabled:bg-primary-300',
  secondary:
    'bg-primary-50 text-primary-700 hover:bg-primary-100 focus:ring-primary-400',
  outline:
    'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',
  ghost:
    'text-slate-600 hover:bg-slate-100 focus:ring-slate-400',
  danger:
    'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500 disabled:bg-red-300',
  success:
    'bg-green-500 text-white hover:bg-green-600 focus:ring-green-500 disabled:bg-green-300',
}

const sizeClasses: Record<Size, string> = {
  xs: 'text-xs px-2.5 py-1.5 rounded-md gap-1',
  sm: 'text-sm px-3 py-2 rounded-lg gap-1.5',
  md: 'text-sm px-4 py-2.5 rounded-lg gap-2',
  lg: 'text-base px-5 py-3 rounded-xl gap-2',
}

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  (
    { variant = 'primary', size = 'md', loading = false, leftIcon, rightIcon, children, className = '', disabled, ...props },
    ref,
  ) => {
    return (
      <button
        ref={ref}
        disabled={disabled || loading}
        className={[
          'inline-flex items-center justify-center font-medium transition-colors',
          'focus:outline-none focus:ring-2 focus:ring-offset-1',
          'disabled:cursor-not-allowed disabled:opacity-60',
          variantClasses[variant],
          sizeClasses[size],
          className,
        ].join(' ')}
        {...props}
      >
        {loading ? (
          <Loader2 size={16} className="animate-spin" />
        ) : leftIcon ? (
          <span className="shrink-0">{leftIcon}</span>
        ) : null}
        {children}
        {!loading && rightIcon && <span className="shrink-0">{rightIcon}</span>}
      </button>
    )
  },
)

Button.displayName = 'Button'
