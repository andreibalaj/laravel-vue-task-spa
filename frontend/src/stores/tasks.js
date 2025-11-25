import { defineStore } from 'pinia'
import api from '@/services/api'

export const useTaskStore = defineStore('tasks', {
  state: () => ({
    tasks: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchTasks() {
      this.loading = true
      try {
        const res = await api.get('/tasks')
        this.tasks = res.data
      } catch {
        this.error = 'Failed to load tasks'
      } finally {
        this.loading = false
      }
    },

    async createTask(title) {
      try {
        this.loading = true
        const res = await api.post('/tasks', { title })
        this.tasks.push(res.data)
      } catch {
        this.error = 'Failed to create task'
      } finally {
        this.loading = false
      }
    },

    async updateTask(task) {
      try {
        this.loading = true
        await api.put(`/tasks/${task.id}`, task)
        Object.assign(
          this.tasks.find((t) => t.id === task.id),
          task,
        )
      } catch {
        this.error = 'Failed to update task'
      } finally {
        this.loading = false
      }
    },

    async deleteTask(id) {
      try {
        this.loading = true
        await api.delete(`/tasks/${id}`)
        this.tasks = this.tasks.filter((t) => t.id !== id)
      } catch {
        this.error = 'Failed to delete task'
      } finally {
        this.loading = false
      }
    },

    reset() {
      this.tasks = []
      this.error = null
    },
  },
})
