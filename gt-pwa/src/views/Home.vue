<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { PencilSquareIcon, DocumentTextIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'

const auth = useAuthStore()
const loading = ref(true)

onMounted(async () => {
    await auth.fetchUser()
    loading.value = false
})
</script>

<template>
  <div class="px-5 py-8 max-w-lg mx-auto">
    <!-- Header / Greeting -->
    <div class="flex items-center justify-between mb-8">
        <div v-if="loading" class="space-y-2">
            <div class="h-8 w-32 bg-gray-200 animate-pulse rounded-lg"></div>
            <div class="h-4 w-48 bg-gray-200 animate-pulse rounded-lg"></div>
        </div>
        <div v-else>
            <h1 class="text-2xl font-black text-gray-900 leading-tight">Halo, {{ auth.user?.name }}!</h1>
            <p class="text-gray-500 text-sm mt-1">Selamat datang di portal pelaporan GT.</p>
        </div>
        <button @click="auth.logout" class="p-2 text-gray-400 hover:text-red-500 transition active:scale-90">
            <ArrowRightOnRectangleIcon class="h-6 w-6" />
        </button>
    </div>

    <!-- Stats Card -->
    <div class="bg-gradient-to-br from-green-600 to-green-500 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-green-200 mb-8 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-xs font-bold uppercase tracking-widest text-green-100 mb-2">Tugas Saat Ini</h2>
            <div v-if="loading" class="space-y-2">
                <div class="h-6 w-40 bg-white/20 animate-pulse rounded"></div>
                <div class="h-4 w-24 bg-white/10 animate-pulse rounded"></div>
            </div>
            <div v-else>
                <p class="text-xl font-bold leading-tight">{{ auth.user?.details?.santri?.penugasan_aktif?.lembaga?.nama || 'Tidak ada penugasan aktif' }}</p>
                <div class="inline-flex items-center mt-3 px-3 py-1 bg-white/20 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/20">
                    {{ auth.user?.details?.is_gt ? 'Guru Tugas' : (auth.user?.details?.is_pjgt ? 'PJGT' : 'Korwil') }}
                </div>
            </div>
        </div>
        <!-- Decorative Circle -->
        <div class="absolute -right-10 -bottom-10 h-40 w-40 bg-white/10 rounded-full"></div>
        <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-white/10 rounded-full"></div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <RouterLink to="/report" class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center space-y-3 active:scale-95 transition-all">
            <div class="h-14 w-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                <PencilSquareIcon class="h-7 w-7" />
            </div>
            <span class="text-sm font-bold text-gray-700">Buat Laporan</span>
        </RouterLink>

        <RouterLink to="/history" class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center space-y-3 active:scale-95 transition-all">
            <div class="h-14 w-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center">
                <DocumentTextIcon class="h-7 w-7" />
            </div>
            <span class="text-sm font-bold text-gray-700">Lihat Riwayat</span>
        </RouterLink>
    </div>

    <!-- Info Section -->
    <div class="bg-blue-50/50 p-6 rounded-[2rem] border border-blue-100/50">
        <div class="flex items-start space-x-4">
            <div class="h-10 w-10 bg-blue-100 text-blue-600 rounded-xl flex-shrink-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-blue-900 mb-1">Informasi Pelaporan</h4>
                <p class="text-xs text-blue-700 leading-relaxed">Pastikan Anda mengirim laporan sebelum akhir periode bulan berjalan. Laporan yang sudah dikirim akan langsung dievaluasi oleh Admin.</p>
            </div>
        </div>
    </div>
  </div>
</template>
