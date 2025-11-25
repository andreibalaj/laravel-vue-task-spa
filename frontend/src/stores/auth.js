// src/stores/auth.js
import { defineStore } from 'pinia'
import api from '@/services/api'
import { useTaskStore } from './tasks'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
    error: null,
  }),

  actions: {
    async register(name, email, password, password_confirmation) {
      this.loading = true
      this.error = null
      try {
        const res = await api.post('/register', {
          name,
          email,
          password,
          password_confirmation,
        })
        this.user = res.data
        sessionStorage.setItem('user_id', res.data.id)
        return true
      } catch (err) {
        this.error = err.response?.data?.error || 'Registration failed'
        return false
      } finally {
        this.loading = false
      }
    },

    async login(email, password) {
      this.loading = true
      this.error = null
      try {
        const res = await api.post('/login', { email, password })
        this.user = res.data
        sessionStorage.setItem('user_id', res.data.id)

        // reset the tasks store for a new user login
        const taskStore = useTaskStore()
        taskStore.reset()

        return true
      } catch (err) {
        this.error = err.response?.data?.error || 'Invalid email or password'
        return false
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.user = null
      sessionStorage.removeItem('user_id')

      // reset the tasks store for a new user login
      const taskStore = useTaskStore()
      taskStore.reset()
    },

    initialize() {
      const userId = sessionStorage.getItem('user_id')
      if (userId) {
        this.user = { user_id: userId }
      }
    },
  },
})
