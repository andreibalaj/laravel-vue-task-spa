<!-- src/views/DashboardView.vue -->
<template>
  <div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">My Tasks</h1>
          <p class="text-gray-600 text-sm mt-1">
            Logged in as <span class="font-medium">{{ auth.user?.name }}</span>
          </p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
          <!-- Refresh Button -->
          <button
            @click="refreshTasks"
            :disabled="taskStore.loading"
            class="flex items-center gap-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            <svg
              v-if="taskStore.loading"
              class="animate-spin h-4 w-4 text-gray-500"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <span>{{ taskStore.loading ? 'Loading...' : 'Refresh' }}</span>
          </button>
          <!-- Logout Button -->
          <button
            @click="logout"
            class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm"
          >
            Logout
          </button>
        </div>
      </div>

      <!-- Add Task Form -->
      <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form @submit.prevent="addTask" class="flex gap-2">
          <input
            v-model="newTask"
            type="text"
            placeholder="Add a new task..."
            class="flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :disabled="taskStore.loading"
            autofocus
          />
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50"
            :disabled="!newTask.trim() || taskStore.loading"
          >
            Add
          </button>
        </form>
      </div>

      <!-- Tasks List -->
      <div v-if="taskStore.loading && tasks.length === 0" class="text-center py-8">
        <div
          class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"
        ></div>
        <p class="mt-2 text-gray-600">Loading your tasks...</p>
      </div>

      <div v-else-if="tasks.length === 0" class="text-center py-8 text-gray-500">
        No tasks yet. Add your first task above!
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="task in tasks"
          :key="task.id"
          class="bg-white rounded-lg shadow-sm p-4 flex items-start gap-3 transition-opacity"
          :class="{ 'opacity-75': taskStore.loading }"
        >
          <input
            type="checkbox"
            v-model="task.completed"
            @change="updateTask(task)"
            class="mt-1 h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
            :disabled="taskStore.loading"
          />

          <div class="flex-1 min-w-0">
            <input
              v-model="task.title"
              @blur="updateTask(task)"
              :class="{
                'line-through text-gray-500': task.completed,
                'text-gray-800': !task.completed,
              }"
              class="w-full bg-transparent border-none outline-none text-lg font-medium"
              :disabled="taskStore.loading"
            />
          </div>

          <button
            @click="deleteTask(task.id)"
            :disabled="taskStore.loading"
            class="text-gray-400 hover:text-red-500 transition disabled:opacity-50"
            aria-label="Delete task"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Global error message -->
      <div v-if="taskStore.error" class="mt-4 p-3 bg-red-50 text-red-700 rounded-md text-sm">
        {{ taskStore.error }}
        <button @click="refreshTasks" class="ml-2 underline">Retry</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTaskStore } from '@/stores/tasks'

const auth = useAuthStore()
const taskStore = useTaskStore()
const router = useRouter()
const newTask = ref('')

const tasks = computed(() => taskStore.tasks || [])

const refreshTasks = () => {
  taskStore.fetchTasks()
}

const addTask = () => {
  if (newTask.value.trim()) {
    taskStore.createTask(newTask.value.trim())
    newTask.value = ''
  }
}

const updateTask = (task) => {
  taskStore.updateTask(task)
}

const deleteTask = (id) => {
  taskStore.deleteTask(id)
}

const logout = () => {
  auth.logout()
  router.push('/login')
}

// Auto-fetch on mount
onMounted(() => {
  refreshTasks()
})
</script>
