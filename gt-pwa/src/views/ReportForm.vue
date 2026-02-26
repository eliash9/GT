<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/utils/api'

const router = useRouter()
const route = useRoute()
const categories = ref<any[]>([])
const answers = ref<Record<string, any>>({})
const loading = ref(true)
const submitting = ref(false)
const submitSuccess = ref(false)

const month = ref(route.query.month ? parseInt(route.query.month as string) : 1)
const year = ref(route.query.year ? parseInt(route.query.year as string) : 1446)
const reportStatus = ref<string | null>(null)
const availableContexts = ref<any[]>([])
const selectedContextIndex = ref(0)

const years = [1446, 1447, 1448]

const isEvaluated = computed(() => reportStatus.value === 'evaluated')

const selectedContext = computed(() => {
    if (availableContexts.value.length > 0 && selectedContextIndex.value >= 0) {
        return availableContexts.value[selectedContextIndex.value]
    }
    return null
})

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

const fetchExistingReport = async () => {
    try {
        const params: any = { period_month: month.value, period_year: year.value }
        if (selectedContext.value) {
            params.santri_id = selectedContext.value.santri_id
            params.lembaga_id = selectedContext.value.lembaga_id
        }
        const res = await api.get('/api/reports/check', { params })
        availableContexts.value = res.data?.available_contexts || []

        if (route.query.santri_id && route.query.lembaga_id) {
            const idx = availableContexts.value.findIndex(c =>
                c.santri_id == route.query.santri_id && c.lembaga_id == route.query.lembaga_id
            )
            if (idx >= 0) selectedContextIndex.value = idx
        }

        if (res.data?.report) {
            reportStatus.value = res.data.report.status
            const existingAnswers = res.data.answers
            categories.value.forEach((cat: any) => {
                cat.questions.forEach((q: any) => {
                    let val = existingAnswers[q.id]
                    if (q.type === 'checkbox') {
                        try { answers.value[q.id] = val ? JSON.parse(val) : [] }
                        catch (e) { answers.value[q.id] = [] }
                    } else {
                        answers.value[q.id] = val || ''
                    }
                })
            })
        } else {
            reportStatus.value = null
            categories.value.forEach((cat: any) => {
                cat.questions.forEach((q: any) => {
                    answers.value[q.id] = q.type === 'checkbox' ? [] : ''
                })
            })
        }
    } catch (e) {
        console.error('Gagal mengecek laporan existing', e)
    }
}

watch([month, year, selectedContextIndex], () => fetchExistingReport())

onMounted(async () => {
    try {
        const res = await api.get('/api/reports/questions')
        categories.value = res.data
        await fetchExistingReport()

        if (availableContexts.value.length === 0) {
            alert('Anda tidak memiliki penugasan aktif saat ini.')
            router.push('/')
            return
        }
        res.data.forEach((cat: any) => {
            cat.questions.forEach((q: any) => {
                answers.value[q.id] = q.type === 'checkbox' ? [] : ''
            })
        })
        await fetchExistingReport()
    } catch (e) {
        alert('Gagal mengambil formulir.')
    } finally {
        loading.value = false
    }
})

const submitReport = async () => {
    if (!confirm('Kirim laporan ini?')) return
    submitting.value = true
    try {
        const data: any = {
            period_month: month.value,
            period_year: year.value,
            answers: answers.value
        }
        if (selectedContext.value) {
            data.santri_id = selectedContext.value.santri_id
            data.lembaga_id = selectedContext.value.lembaga_id
            data.pjgt_id = selectedContext.value.pjgt_id
        }
        await api.post('/api/reports/submit', data)
        submitSuccess.value = true
        setTimeout(() => router.push('/history'), 1800)
    } catch (e: any) {
        alert(e.response?.data?.message || 'Gagal mengirim laporan.')
    } finally {
        submitting.value = false
    }
}
</script>

<template>
  <div class="min-h-dvh" style="padding-top: env(safe-area-inset-top)">
    <!-- Header — same pattern as Home -->
    <div class="pattern-bg px-5 pt-6 pb-14 relative overflow-hidden">
      <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/5"></div>
      <div class="absolute -right-6 top-8 w-28 h-28 rounded-full bg-white/5"></div>
      <div class="relative z-10">
        <p class="text-emerald-300 text-[10px] font-black uppercase tracking-widest mb-1">Formulir Pelaporan</p>
        <h1 class="text-white text-xl font-extrabold">Laporan</h1>
        <p class="text-emerald-200 text-xs mt-1">Isi sesuai kegiatan periode ini</p>
      </div>
    </div>

    <!-- Body — same overlap pattern as Home -->
    <div class="relative z-10 px-5 -mt-6 pb-32 space-y-4">

      <!-- Skeleton loading -->
      <div v-if="loading" class="space-y-4 mt-2">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl h-28 animate-pulse border border-gray-100"></div>
      </div>

      <div v-else>
        <!-- Period & Context Card -->
        <div class="bg-gradient-to-br from-[#0f7a5a] to-[#16a374] rounded-2xl p-5 shadow-lg shadow-emerald-100 border border-emerald-500/20">
          <p class="text-emerald-100 text-[10px] font-black uppercase tracking-widest mb-3">Periode & Target</p>

          <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
              <label class="text-[10px] font-bold text-emerald-200 uppercase block mb-1">Bulan Hijri</label>
              <select v-model="month" class="w-full bg-white/15 border-0 text-white text-sm rounded-xl py-2.5 px-3 focus:ring-2 focus:ring-white/50 focus:outline-none">
                <option v-for="m in months" :key="m.value" :value="m.value" class="text-gray-800">{{ m.label }}</option>
              </select>
            </div>
            <div>
              <label class="text-[10px] font-bold text-emerald-200 uppercase block mb-1">Tahun Hijri</label>
              <select v-model="year" class="w-full bg-white/15 border-0 text-white text-sm rounded-xl py-2.5 px-3 focus:ring-2 focus:ring-white/50 focus:outline-none">
                <option v-for="y in years" :key="y" :value="y" class="text-gray-800">{{ y }} H</option>
              </select>
            </div>
          </div>

          <div v-if="availableContexts.length > 1">
            <label class="text-[10px] font-bold text-emerald-200 uppercase block mb-1">Pilih Lembaga & GT</label>
            <select v-model="selectedContextIndex" class="w-full bg-white/15 border-0 text-white text-sm rounded-xl py-2.5 px-3 focus:ring-2 focus:ring-white/50 focus:outline-none">
              <option v-for="(ctx, idx) in availableContexts" :key="idx" :value="idx" class="text-gray-800">
                {{ ctx.santri_name || 'N/A' }} · {{ ctx.lembaga_name }}
              </option>
            </select>
          </div>
          <div v-else-if="availableContexts.length === 1" class="bg-white/10 rounded-xl p-3 border border-white/15">
            <p class="text-[9px] text-emerald-200 font-black uppercase tracking-widest mb-0.5">Target Otomatis</p>
            <p class="text-white text-sm font-bold">{{ availableContexts[0].santri_name || 'N/A' }}</p>
            <p class="text-emerald-100 text-xs opacity-80">{{ availableContexts[0].lembaga_name }}</p>
          </div>
        </div>

        <!-- Already evaluated notice -->
        <div v-if="isEvaluated" class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <div class="text-xs">
            <p class="font-bold text-blue-800 mb-0.5">Laporan sudah dievaluasi</p>
            <p class="text-blue-600">Laporan periode ini telah disetujui admin dan tidak dapat diubah.</p>
          </div>
        </div>

        <!-- Form Categories -->
        <form @submit.prevent="submitReport" class="space-y-5">
          <div v-for="cat in categories" :key="cat.id">
            <div class="flex items-center gap-2 mb-3">
              <div class="h-3 w-1 bg-[#0f7a5a] rounded-full"></div>
              <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ cat.name }}</h2>
            </div>

            <div class="space-y-3">
              <div v-for="q in cat.questions" :key="q.id"
                class="bg-white rounded-2xl p-5 border border-gray-100/80 shadow-sm"
                :class="isEvaluated ? 'opacity-60' : ''">
                <label class="block text-sm font-bold text-gray-800 mb-3 leading-snug">
                  {{ q.question }}
                  <span v-if="q.is_required" class="text-red-400 ml-0.5">*</span>
                </label>

                <fieldset :disabled="isEvaluated" class="contents">
                  <template v-if="q.type === 'text'">
                    <input v-model="answers[q.id]" :required="q.is_required" type="text"
                      class="w-full bg-[#f0f5f2] border-0 rounded-xl py-3 px-4 text-sm text-gray-700 focus:ring-2 focus:ring-[#0f7a5a] transition"
                      placeholder="Tulis jawaban..." />
                  </template>

                  <template v-else-if="q.type === 'textarea'">
                    <textarea v-model="answers[q.id]" :required="q.is_required"
                      class="w-full bg-[#f0f5f2] border-0 rounded-xl py-3 px-4 text-sm text-gray-700 focus:ring-2 focus:ring-[#0f7a5a] transition resize-none"
                      rows="3" placeholder="Jelaskan detail..."></textarea>
                  </template>

                  <template v-else-if="q.type === 'select'">
                    <select v-model="answers[q.id]" :required="q.is_required"
                      class="w-full bg-[#f0f5f2] border-0 rounded-xl py-3 px-4 text-sm text-gray-700 focus:ring-2 focus:ring-[#0f7a5a] transition">
                      <option value="">Pilih Opsi</option>
                      <option v-for="opt in q.options" :key="opt" :value="opt">{{ opt }}</option>
                    </select>
                  </template>

                  <template v-else-if="q.type === 'radio'">
                    <div class="space-y-2">
                      <label v-for="opt in q.options" :key="opt"
                        class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                        :class="answers[q.id] === opt ? 'border-[#0f7a5a] bg-emerald-50/50' : 'border-gray-100 bg-[#f0f5f2] hover:border-emerald-200'">
                        <div class="w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center"
                          :class="answers[q.id] === opt ? 'border-[#0f7a5a]' : 'border-gray-300'">
                          <div v-if="answers[q.id] === opt" class="w-2 h-2 rounded-full bg-[#0f7a5a]"></div>
                        </div>
                        <input type="radio" :name="'q_' + q.id" :value="opt" v-model="answers[q.id]" class="hidden" />
                        <span class="text-sm text-gray-700">{{ opt }}</span>
                      </label>
                    </div>
                  </template>

                  <template v-else-if="q.type === 'checkbox'">
                    <div class="space-y-2">
                      <label v-for="opt in q.options" :key="opt"
                        class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                        :class="answers[q.id].includes(opt) ? 'border-[#0f7a5a] bg-emerald-50/50' : 'border-gray-100 bg-[#f0f5f2] hover:border-emerald-200'">
                        <div class="w-4 h-4 rounded flex-shrink-0 border-2 flex items-center justify-center"
                          :class="answers[q.id].includes(opt) ? 'border-[#0f7a5a] bg-[#0f7a5a]' : 'border-gray-300'">
                          <svg v-if="answers[q.id].includes(opt)" class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                          </svg>
                        </div>
                        <input type="checkbox" :value="opt" v-model="answers[q.id]" class="hidden" />
                        <span class="text-sm text-gray-700">{{ opt }}</span>
                      </label>
                    </div>
                  </template>
                </fieldset>
              </div>
            </div>
          </div>

          <!-- Submit -->
          <button v-if="!isEvaluated" type="submit" :disabled="submitting"
            class="w-full py-4 rounded-xl font-extrabold text-sm transition-all pressable"
            :class="submitSuccess
              ? 'bg-emerald-500 text-white'
              : submitting
                ? 'bg-gray-400 text-white'
                : 'bg-gradient-to-r from-[#0f7a5a] to-[#16a374] text-white shadow-lg shadow-emerald-200'">
            <span v-if="submitSuccess" class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Laporan Terkirim!
            </span>
            <span v-else-if="submitting" class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-4 h-4 text-white" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Mengirim...
            </span>
            <span v-else>
              {{ reportStatus === 'submitted' ? 'Update Laporan' : 'Kirim Laporan' }}
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
