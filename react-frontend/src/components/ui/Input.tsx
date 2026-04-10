import { InputHTMLAttributes, forwardRef } from 'react'

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
  label?: string
  error?: string
  hint?: string
  leftAddon?: React.ReactNode
  rightAddon?: React.ReactNode
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ label, error, hint, leftAddon, rightAddon, className = '', id, ...props }, ref) => {
    const inputId = id || label?.toLowerCase().replace(/\s+/g, '-')

    return (
      <div className="w-full">
        {label && (
          <label htmlFor={inputId} className="form-label">
            {label}
            {props.required && <span className="text-red-500 ml-1">*</span>}
          </label>
        )}
        <div className="relative flex items-center">
          {leftAddon && (
            <div className="absolute left-3 text-slate-400 pointer-events-none">{leftAddon}</div>
          )}
          <input
            ref={ref}
            id={inputId}
            className={[
              'w-full rounded-lg border bg-white text-slate-900 placeholder:text-slate-400',
              'text-sm py-2.5 px-3 transition-colors',
              'focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400',
              'disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed',
              error
                ? 'border-red-400 focus:ring-red-300 focus:border-red-400'
                : 'border-slate-300',
              leftAddon ? 'pl-9' : '',
              rightAddon ? 'pr-9' : '',
              className,
            ].join(' ')}
            {...props}
          />
          {rightAddon && (
            <div className="absolute right-3 text-slate-400">{rightAddon}</div>
          )}
        </div>
        {error && <p className="form-error">{error}</p>}
        {hint && !error && <p className="text-xs text-slate-500 mt-1">{hint}</p>}
      </div>
    )
  },
)

Input.displayName = 'Input'
