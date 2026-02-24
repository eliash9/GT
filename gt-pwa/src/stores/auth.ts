import { defineStore } from 'pinia'
import api from '@/utils/api'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('gt_token') || null)
    const user = ref<any>(null)

    const setToken = (newToken: string) => {
        token.value = newToken
        localStorage.setItem('gt_token', newToken)
    }

    const logout = () => {
        token.value = null
        user.value = null
        localStorage.removeItem('gt_token')
        window.location.href = '/login'
    }

    const fetchUser = async () => {
        try {
            const res = await api.get('/api/me')
            user.value = res.data
        } catch (e) {
            logout()
        }
    }

    return { token, user, setToken, logout, fetchUser }
})
