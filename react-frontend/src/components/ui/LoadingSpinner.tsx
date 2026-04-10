import { Loader2 } from 'lucide-react'

interface LoadingSpinnerProps {
  size?: number
  className?: string
  fullPage?: boolean
}

export function LoadingSpinner({ size = 24, className = '', fullPage = false }: LoadingSpinnerProps) {
  if (fullPage) {
    return (
      <div className="fixed inset-0 flex items-center justify-center bg-white/80">
        <Loader2 size={size} className={['animate-spin text-primary-500', className].join(' ')} />
      </div>
    )
  }

  return (
    <div className="flex items-center justify-center p-8">
      <Loader2 size={size} className={['animate-spin text-primary-500', className].join(' ')} />
    </div>
  )
}
