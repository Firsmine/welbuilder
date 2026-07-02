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
      meta: { title: 'Login — WelBuilder', requiresGuest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => RegisterView,
      meta: { title: 'Register — WelBuilder', requiresGuest: true },
    },
    {
      path: '/pages',
      name: 'pages',
      component: () => PagesView,
      meta: { title: 'Pages — WelBuilder', requiresAuth: true },
    },
    {
      path: '/pages/:slug/builder',
      name: 'builder',
      component: () => BuilderView,
      meta: { title: 'Page Builder — WelBuilder', requiresAuth: true },
    },
    {
      path: '/pages/:slug/preview',
      name: 'preview',
      component: () => PreviewView,
      meta: { title: 'Page Preview — WelBuilder', requiresAuth: true },
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/login',
    },
  ],
})

export default router
