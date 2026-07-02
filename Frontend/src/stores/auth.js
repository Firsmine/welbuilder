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
    async login(credentials) {
      this.errors = null
      try {
        const response = await api.post('/login', credentials)
        this.setAuth(response.data.data)
        return true
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors
        } else if (error.response && error.response.status === 401) {
          this.errors = { message: error.response.data.message }
        }
        return false
      }
    },
    async logout() {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
      }
    },
    setAuth(data) {
      this.user = {
        id: data.id,
        name: data.name,
        email: data.email,
      }
      this.token = data.token
      localStorage.setItem('user', JSON.stringify(this.user))
      localStorage.setItem('token', this.token)
    },
  },
})
