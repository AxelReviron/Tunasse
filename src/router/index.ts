import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import TabsPage from '../views/TabsPage.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/tabs/dashboard'
  },
  {
    path: '/tabs/',
    component: TabsPage,
    children: [
      {
        path: '',
        redirect: '/tabs/dashboard'
      },
      {
        path: 'dashboard',
        component: () => import('../views/Dashboard.vue')
      },
      {
        path: 'transactions',
        component: () => import('../views/Transactions.vue')
      },
      {
        path: 'budget',
        component: () => import('../views/Budget.vue')
      },
      {
        path: 'account',
        component: () => import('../views/Account.vue')
      },
      {
        path: 'settings',
        component: () => import('../views/Settings.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
