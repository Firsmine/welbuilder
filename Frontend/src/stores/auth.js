import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    errors: null,
  }),
  actions: {
    async register(userData) {
      this.errors = null
      try {
        const response = await api.post('/register', userData)
        this.setAuth(response.data.data)
        return true
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors
        }
        return false
      }
    },
  },
})
