// src/services/api.js
import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

// Automatically attach X-User-ID from sessionStorage
api.interceptors.request.use((config) => {
  const userId = sessionStorage.getItem('user_id')
  if (userId) {
    config.headers['X-User-ID'] = userId
  }
  return config
})

export default api