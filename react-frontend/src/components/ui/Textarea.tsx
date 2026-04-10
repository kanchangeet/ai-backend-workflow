import { TextareaHTMLAttributes, forwardRef } from 'react'

interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
  label?: string
  error?: string
  hint?: string
}

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaProps>(
  ({ label, error, hint, className = '', id, ...props }, ref) => {
    const inputId = id || label?.toLowerCase().replace(/\s+/g, '-')

    return (
      <div className="w-full">
        {label && (
          <label htmlFor={inputId} className="form-label">
            {label}
            {props.required && <span className="text-red-500 ml-1">*</span>}
          </label>
        )}
        <textarea
          ref={ref}
          id={inputId}
          rows={4}
          className={[
            'w-full rounded-lg border bg-white text-slate-900 placeholder:text-slate-400',
            'text-sm py-2.5 px-3 transition-colors resize-y',
            'focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400',
            'disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed',
            error ? 'border-red-400 focus:ring-red-300' : 'border-slate-300',
            className,
          ].join(' ')}
          {...props}
        />
        {error && <p className="form-error">{error}</p>}
        {hint && !error && <p className="text-xs text-slate-500 mt-1">{hint}</p>}
      </div>
    )
  },
)

Textarea.displayName = 'Textarea'
