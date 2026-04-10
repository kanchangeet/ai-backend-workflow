import { createBrowserRouter, Navigate } from 'react-router-dom'
import { AppLayout } from '@/layouts/AppLayout'
import { AuthLayout } from '@/layouts/AuthLayout'
import { LoginPage } from '@/pages/auth/LoginPage'
import { RegisterPage } from '@/pages/auth/RegisterPage'
import { DashboardPage } from '@/pages/DashboardPage'
import { MasterListPage } from '@/pages/master/MasterListPage'
import { MasterCreatePage } from '@/pages/master/MasterCreatePage'
import { MasterEditPage } from '@/pages/master/MasterEditPage'
import { PreviewPage } from '@/pages/preview/PreviewPage'

export const router = createBrowserRouter([
  {
    path: '/',
    element: <Navigate to="/category" replace />,
  },
  {
    element: <AuthLayout />,
    children: [
      { path: '/login', element: <LoginPage /> },
      { path: '/register', element: <RegisterPage /> },
    ],
  },
  {
    element: <AppLayout />,
    children: [
      { path: '/dashboard', element: <DashboardPage /> },
      { path: '/category', element: <MasterListPage /> },
      { path: '/category/create', element: <MasterCreatePage /> },
      { path: '/category/:id/edit', element: <MasterEditPage /> },
      { path: '/preview', element: <PreviewPage /> },
    ],
  },
  {
    path: '*',
    element: <Navigate to="/category" replace />,
  },
])
