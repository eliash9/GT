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
const showPass = ref(false)

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
  <div class="min-h-dvh flex flex-col" style="padding-top: env(safe-area-inset-top)">
    <!-- Top Banner -->
    <div class="pattern-bg relative overflow-hidden px-6 pt-14 pb-16">
      <!-- Deco circles -->
      <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/5"></div>
      <div class="absolute -left-8 bottom-0 w-32 h-32 rounded-full bg-white/5"></div>
      
      <div class="relative z-10 text-center">
        <!-- Crescent-star icon (islamic inspired) -->
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
          <svg viewBox="0 0 40 40" class="w-9 h-9 text-white" fill="currentColor">
            <!-- Stylized book/mosque icon -->
            <path d="M20 4C11.16 4 4 11.16 4 20C4 28.84 11.16 36 20 36C28.84 36 36 28.84 36 20C36 11.16 28.84 4 20 4ZM20 8C24.5 8 28.5 10.3 31 13.8L28 16L25 13C24 12 22.5 11.5 20 11.5C17.5 11.5 16 12 15 13L12 16L9 13.8C11.5 10.3 15.5 8 20 8ZM12 20L15 17L18 20L15 23L12 20ZM20 32C15.5 32 11.5 29.7 9 26.2L12 24L15 27C16 28 17.5 28.5 20 28.5C22.5 28.5 24 28 25 27L28 24L31 26.2C28.5 29.7 24.5 32 20 32ZM28 20L25 23L22 20L25 17L28 20Z" opacity="0.9"/>
          </svg>
        </div>
        <h1 class="text-white text-2xl font-extrabold mb-1">SiGT Mobile</h1>
        <p class="text-emerald-200 text-sm">Portal Kegiatan Guru Tugas</p>
      </div>
    </div>

    <!-- Card Form -->
    <div class="flex-1 bg-[#f0f5f2] px-5 pt-6">
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100/70 p-6">
        <h2 class="text-base font-extrabold text-gray-900 mb-5">Masuk ke Akun</h2>

        <!-- Error -->
        <Transition name="page">
          <div v-if="errorMsg" class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span class="text-red-600 text-xs font-semibold">{{ errorMsg }}</span>
          </div>
        </Transition>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- Email -->
          <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Email / ID Akun</label>
            <input
              v-model="email"
              type="text"
              required
              placeholder="contoh@email.com"
              class="w-full bg-[#f0f5f2] border border-gray-100 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0f7a5a] focus:border-transparent transition"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kata Sandi</label>
            <div class="relative">
              <input
                v-model="password"
                :type="showPass ? 'text' : 'password'"
                required
                placeholder="••••••••"
                class="w-full bg-[#f0f5f2] border border-gray-100 rounded-xl px-4 py-3.5 pr-11 text-sm font-medium text-gray-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0f7a5a] focus:border-transparent transition"
              />
              <button type="button" @click="showPass = !showPass"
                class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-gray-400 pressable">
                <svg v-if="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-4 rounded-xl font-extrabold text-sm text-white transition-all pressable disabled:opacity-60 mt-2"
            :class="loading ? 'bg-gray-400' : 'bg-gradient-to-r from-[#0f7a5a] to-[#16a374] shadow-lg shadow-emerald-200'"
          >
            <span v-if="!loading">Masuk Sekarang</span>
            <span v-else class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-4 h-4 text-white" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Mohon tunggu...
            </span>
          </button>
        </form>
      </div>

      <!-- Footer -->
      <p class="text-center text-[10px] text-gray-400 mt-6 pb-8">
        © 2025 SiGT · Sistem Informasi Guru Tugas
      </p>
    </div>
  </div>
</template>
