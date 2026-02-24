<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/utils/api'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMsg = ref('')

const handleLogin = async () => {
    loading.value = true
    errorMsg.value = ''
    try {
        const res = await api.post('/api/login', {
            email: email.value,
            password: password.value,
            device_name: 'PWA_' + navigator.userAgent.substring(0, 10)
        })
        auth.setToken(res.data.token)
        router.push('/')
    } catch (e: any) {
        errorMsg.value = e.response?.data?.message || 'Gagal masuk. Cek koneksi Anda.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
  <div class="p-6 h-screen flex flex-col justify-center items-center bg-gray-50 font-sans">
    <div class="w-full max-w-sm bg-white p-8 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.04)] border border-gray-100">
        <div class="flex justify-center mb-6">
            <div class="h-16 w-16 bg-green-100 rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.355r2.773-1.515a12.936 12.936 0 005.845-6.144A12.936 12.936 0 0012 2.944a12.936 12.936 0 00-8.618 10.752 12.936 12.936 0 005.845 6.144L12 21.355z" />
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-2">SiGT Mobile</h1>
        <p class="text-center text-gray-500 mb-8 text-sm">Masuk untuk melaporkan kegiatan dakwah.</p>
        
        <div v-if="errorMsg" class="mb-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 animate-pulse">
            {{ errorMsg }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email / ID Akun</label>
                <input v-model="email" type="text" required class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 py-3.5 px-4 border bg-gray-50/50 transition" placeholder="Masukkan ID">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kata Sandi</label>
                <input v-model="password" type="password" required class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 py-3.5 px-4 border bg-gray-50/50 transition" placeholder="••••••••">
            </div>
            <button :disabled="loading" type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mt-8 transition-all active:scale-95 disabled:opacity-50">
                <span v-if="!loading">Masuk Sekarang</span>
                <span v-else class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mohon tunggu...
                </span>
            </button>
        </form>
    </div>
  </div>
</template>
