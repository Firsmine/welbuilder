import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import BuilderView from '@/views/BuilderView.vue'
import PagesView from '@/views/PagesView.vue'
import PreviewView from '@/views/PreviewView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // {
    //   path: '/about',
    //   name: 'about',
    //   component: () =>
    // },
  ],
})

export default router
