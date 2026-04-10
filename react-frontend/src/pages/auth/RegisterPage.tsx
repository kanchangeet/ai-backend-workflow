import { useForm } from 'react-hook-form'
import { Link } from 'react-router-dom'
import { Mail, Lock, User } from 'lucide-react'
import { useRegister } from '@/hooks/useAuth'
import { Button, Input, Alert } from '@/components/ui'
import { RegisterPayload } from '@/api/auth'

export function RegisterPage() {
  const register_ = useRegister()

  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm<RegisterPayload>()

  const password = watch('password')

  const onSubmit = (data: RegisterPayload) => {
    register_.mutate(data)
  }

  return (
    <div>
      <h2 className="text-xl font-bold text-slate-900 mb-1">Create account</h2>
      <p className="text-sm text-slate-500 mb-6">Fill in the details below to get started</p>

      {register_.error && (
        <Alert variant="error" className="mb-4">
          {(register_.error as Error).message || 'Registration failed. Please try again.'}
        </Alert>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4" data-testid="register-form">
        <Input
          label="Full Name"
          type="text"
          placeholder="John Doe"
          leftAddon={<User size={16} />}
          error={errors.name?.message}
          data-testid="name-input"
          {...register('name', { required: 'Name is required' })}
        />

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

        <Input
          label="Confirm Password"
          type="password"
          placeholder="••••••••"
          leftAddon={<Lock size={16} />}
          error={errors.password_confirmation?.message}
          data-testid="password-confirm-input"
          {...register('password_confirmation', {
            required: 'Please confirm your password',
            validate: (val) => val === password || 'Passwords do not match',
          })}
        />

        <Button
          type="submit"
          className="w-full"
          loading={register_.isPending}
          data-testid="register-submit"
        >
          Create Account
        </Button>
      </form>

      <p className="text-center text-sm text-slate-500 mt-6">
        Already have an account?{' '}
        <Link to="/login" className="text-primary-600 font-medium hover:underline">
          Sign in
        </Link>
      </p>
    </div>
  )
}
