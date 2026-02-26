<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/api'

const router = useRouter()
const reports = ref<any[]>([])
const loading = ref(true)

const months = [
    { value: 1, label: "Syawal - Dzulqo'dah" },
    { value: 2, label: "Dzulqo'dah - Dzulhijjah" },
    { value: 3, label: "Dzulhijjah - Muharam" },
    { value: 4, label: "Muharam - Safar" },
    { value: 5, label: "Safar - Rabi'ul Awal" },
    { value: 6, label: "Rabi'ul Awal - Rabi'ul Akhir" },
    { value: 7, label: "Rabi'ul Akhir - Jumadil Awal" },
    { value: 8, label: "Jumadil Awal - Jumadil Akhir" },
    { value: 9, label: "Jumadil Akhir - Rajab" },
    { value: 10, label: "Rajab - Sya'ban" },
]

const getPeriodLabel = (val: number) => months.find(m => m.value === val)?.label ?? val

const statusConfig: any = {
    submitted: { label: 'Terkirim', bg: 'bg-blue-50', text: 'text-blue-700', icon: 'M5 13l4 4L19 7' },
    evaluated: { label: 'Dievaluasi', bg: 'bg-emerald-50', text: 'text-emerald-700', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    draft:     { label: 'Draft', bg: 'bg-gray-50', text: 'text-gray-500', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5' },
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
  <div class="min-h-dvh" style="padding-top: env(safe-area-inset-top)">
    <!-- Header — same pattern as Home -->
    <div class="pattern-bg px-5 pt-6 pb-14 relative overflow-hidden">
      <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/5"></div>
      <div class="absolute -right-6 top-8 w-28 h-28 rounded-full bg-white/5"></div>
      <div class="relative z-10">
        <p class="text-emerald-300 text-[10px] font-black uppercase tracking-widest mb-1">Arsip Laporan</p>
        <h1 class="text-white text-xl font-extrabold">Riwayat</h1>
      </div>
    </div>

    <!-- Content — same overlap pattern as Home -->
    <div class="relative z-10 px-5 -mt-6 pb-24">
      <!-- Loading skeletons -->
      <div v-if="loading" class="space-y-3 mt-2">
        <div v-for="i in 5" :key="i" class="bg-white rounded-2xl h-20 animate-pulse border border-gray-100"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="reports.length === 0"
        class="flex flex-col items-center justify-center pt-24 text-center">
        <div class="w-20 h-20 bg-[#f0f5f2] rounded-full flex items-center justify-center mb-4 border-4 border-white shadow-sm">
          <svg class="w-9 h-9 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <p class="font-bold text-gray-500">Belum ada laporan</p>
        <p class="text-xs text-gray-400 mt-1">Laporan yang dikirim akan tampil di sini</p>
        <button @click="router.push('/report')"
          class="mt-6 px-6 py-3 bg-gradient-to-r from-[#0f7a5a] to-[#16a374] text-white rounded-xl font-bold text-sm pressable shadow-lg shadow-emerald-100">
          Buat Laporan Pertama
        </button>
      </div>

      <!-- Report list -->
      <div v-else class="space-y-3 mt-2">
        <div
          v-for="report in reports"
          :key="report.id"
          @click="router.push({ name: 'Report', query: { month: report.period_month, year: report.period_year, santri_id: report.santri_id, lembaga_id: report.lembaga_id } })"
          class="bg-white rounded-2xl border border-gray-100/80 shadow-sm pressable cursor-pointer overflow-hidden"
        >
          <div class="flex items-stretch">
            <!-- Left color strip -->
            <div class="w-1 flex-shrink-0 rounded-l-2xl"
              :class="report.status === 'evaluated' ? 'bg-emerald-400' : report.status === 'submitted' ? 'bg-blue-400' : 'bg-gray-300'">
            </div>
            
            <div class="flex-1 p-4 flex items-center justify-between gap-3">
              <div class="min-w-0">
                <!-- Lembaga if available -->
                <p v-if="report.lembaga" class="text-[10px] font-black uppercase tracking-widest text-[#0f7a5a] mb-0.5">
                  {{ report.lembaga?.nama }}
                </p>
                <!-- Period -->
                <h3 class="font-bold text-gray-800 text-sm truncate">{{ getPeriodLabel(report.period_month) }}</h3>
                <!-- GT Name -->
                <p v-if="report.santri" class="text-xs text-gray-500 mt-0.5">GT: {{ report.santri?.nama }}</p>
                <!-- Date -->
                <p class="text-[10px] text-gray-400 mt-1">{{ report.period_year }} H · {{ new Date(report.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</p>
              </div>

              <!-- Status badge -->
              <div class="flex-shrink-0">
                <span class="text-[10px] font-black px-2.5 py-1 rounded-lg"
                  :class="[
                    statusConfig[report.status]?.bg ?? 'bg-gray-50',
                    statusConfig[report.status]?.text ?? 'text-gray-500'
                  ]">
                  {{ statusConfig[report.status]?.label ?? report.status }}
                </span>
              </div>
            </div>

            <!-- Right arrow -->
            <div class="flex items-center pr-3 text-gray-300">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
