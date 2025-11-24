// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import DashboardView from '@/views/DashboardView.vue' // you'll create this next
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', name: 'Login', component: LoginView },
  { path: '/register', name: 'Register', component: RegisterView },
  { path: '/dashboard', name: 'Dashboard', component: DashboardView },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Global navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const userId = sessionStorage.getItem('user_id');
  const isAuthenticated = userId && !['undefined', 'null'].includes(userId);

  // Restore user in store if authenticated
  if (isAuthenticated && !authStore.user) {
    authStore.user = { user_id: sessionStorage.getItem('user_id') }
  }

  const publicRoutes = ['Login', 'Register']
  const isPublicRoute = publicRoutes.includes(to.name)

  if (isAuthenticated && isPublicRoute) {
    // Authenticated user tries to access login/register → redirect to dashboard
    return next({ name: 'Dashboard' })
  }

  if (!isAuthenticated && !isPublicRoute) {
    // Unauthenticated user tries to access protected route → redirect to login
    return next({ name: 'Login' })
  }

  // All other cases: allow navigation
  next()
})


export default router