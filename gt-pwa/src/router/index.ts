import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            name: 'Login',
            component: () => import('@/views/Login.vue'),
            meta: { guest: true }
        },
        {
            path: '/',
            component: () => import('@/layouts/AppLayout.vue'),
            meta: { requiresAuth: true },
            children: [
                {
                    path: '',
                    name: 'Home',
                    component: () => import('@/views/Home.vue')
                },
                {
                    path: 'report',
                    name: 'Report',
                    component: () => import('@/views/ReportForm.vue')
                },
                {
                    path: 'history',
                    name: 'History',
                    component: () => import('@/views/ReportHistory.vue')
                }
            ]
        }
    ]
})

router.beforeEach((to, _from) => {
    const auth = useAuthStore()
    if (to.meta.requiresAuth && !auth.token) {
        return { name: 'Login' }
    }
    if (to.meta.guest && auth.token) {
        return { name: 'Home' }
    }
    return true
})

export default router
