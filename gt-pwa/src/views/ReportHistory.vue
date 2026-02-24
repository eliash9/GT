<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/utils/api'

const reports = ref<any[]>([])
const loading = ref(true)

const getPeriodLabel = (val: number) => {
    const months = [
        { value: 1, label: "Syawal - Dzulqo'dah" },
        { value: 2, label: "Dzulqo'dah - Dzulhijjah" },
        { value: 3, label: "Dzulhijjah - Muhram" },
        { value: 4, label: "Muharam - Safar" },
        { value: 5, label: "Safar - Rabi'ul Awal" },
        { value: 6, label: "Rabi'ul Awal - Rabi'ul Akhir" },
        { value: 7, label: "Rabi'ul Akhir - Jumadil Awal" },
        { value: 8, label: "Jumadil Awal - Jumadil Akhir" },
        { value: 9, label: "Jumadil Akhir - Rajab" },
        { value: 10, label: "Rajab - Sya'ban" },
    ]
    const found = months.find(m => m.value === val)
    return found ? found.label : val
}

onMounted(async () => {
    try {
        const res = await api.get('/api/reports/history')
        reports.value = res.data
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
})
</script>

<template>
  <div class="px-5 py-8 max-w-lg mx-auto pb-24">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-900 leading-tight">Riwayat Laporan</h1>
        <p class="text-gray-500 text-sm mt-1">Daftar laporan yang telah Anda kirimkan.</p>
    </div>

    <div v-if="loading" class="space-y-4">
        <div v-for="i in 5" :key="i" class="h-24 bg-gray-100 animate-pulse rounded-2xl"></div>
    </div>

    <div v-else-if="reports.length === 0" class="flex flex-col items-center justify-center pt-20 text-center opacity-50">
        <div class="h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <p class="font-bold">Belum ada riwayat</p>
        <p class="text-xs">Laporan yang Anda kirim akan muncul di sini.</p>
    </div>

    <div v-else class="space-y-4">
        <div v-for="report in reports" :key="report.id" class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex-1 min-w-0 pr-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-green-600 mb-1">Diterima</p>
                <h3 class="font-bold text-gray-800 truncate">{{ getPeriodLabel(report.period_month) }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ report.period_year }} Hijri • {{ new Date(report.created_at).toLocaleDateString() }}</p>
            </div>
            <div class="flex-shrink-0">
                <div v-if="report.status === 'evaluated'" class="h-8 w-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div v-else class="h-8 w-8 bg-green-50 text-green-600 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>
