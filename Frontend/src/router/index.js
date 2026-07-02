import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import BuilderView from '@/views/BuilderView.vue'
import PagesView from '@/views/PagesView.vue'
import PreviewView from '@/views/PreviewView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: () => RegisterView,
    },
    {
      path: '/pages',
      name: 'pages',
      component: () => PagesView,
    },
    {
      path: '/pages/:slug/builder',
      name: 'builder',
      component: () => BuilderView,
    },
    {
      path: '/pages/:slug/preview',
      name: 'preview',
      component: () => PreviewView,
    },
  ],
})

export default router
