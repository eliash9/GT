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

router.beforeEach((to, _from, next) => {
    const auth = useAuthStore()
    if (to.meta.requiresAuth && !auth.token) {
        next({ name: 'Login' })
    } else if (to.meta.guest && auth.token) {
        next({ name: 'Home' })
    } else {
        next()
    }
})

export default router
