import { useForm } from 'react-hook-form'
import { Link } from 'react-router-dom'
import { Mail, Lock } from 'lucide-react'
import { useLogin } from '@/hooks/useAuth'
import { Button, Input, Alert } from '@/components/ui'
import { LoginPayload } from '@/api/auth'

export function LoginPage() {
  const login = useLogin()

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<LoginPayload>()

  const onSubmit = (data: LoginPayload) => {
    login.mutate(data)
  }

  return (
    <div>
      <h2 className="text-xl font-bold text-slate-900 mb-1">Sign in</h2>
      <p className="text-sm text-slate-500 mb-6">Enter your credentials to continue</p>

      {login.error && (
        <Alert variant="error" className="mb-4">
          {(login.error as Error).message || 'Invalid credentials. Please try again.'}
        </Alert>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4" data-testid="login-form">
        <Input
          label="Email"
          type="email"
          placeholder="you@example.com"
          leftAddon={<Mail size={16} />}
          error={errors.email?.message}
          data-testid="email-input"
          {...register('email', {
            required: 'Email is required',
            pattern: { value: /^\S+@\S+\.\S+$/, message: 'Invalid email' },
          })}
        />

        <Input
          label="Password"
          type="password"
          placeholder="••••••••"
          leftAddon={<Lock size={16} />}
          error={errors.password?.message}
          data-testid="password-input"
          {...register('password', {
            required: 'Password is required',
            minLength: { value: 6, message: 'Minimum 6 characters' },
          })}
        />

        <Button
          type="submit"
          className="w-full"
          loading={login.isPending}
          data-testid="login-submit"
        >
          Sign in
        </Button>
      </form>

      <p className="text-center text-sm text-slate-500 mt-6">
        Don&apos;t have an account?{' '}
        <Link to="/register" className="text-primary-600 font-medium hover:underline">
          Create one
        </Link>
      </p>
    </div>
  )
}
