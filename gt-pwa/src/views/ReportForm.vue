<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/api'

const router = useRouter()
const categories = ref<any[]>([])
const answers = ref<Record<string, any>>({})
const loading = ref(true)
const submitting = ref(false)

const month = ref(1) // Default Syawal
const year = ref(1446) // Default Hijri year 

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

onMounted(async () => {
    try {
        const res = await api.get('/api/reports/questions')
        categories.value = res.data
        
        // Init answers
        res.data.forEach((cat: any) => {
            cat.questions.forEach((q: any) => {
                answers.value[q.id] = q.type === 'checkbox' ? [] : ''
            })
        })
    } catch (e) {
        alert('Gagal mengambil formulir.')
    } finally {
        loading.value = false
    }
})

const submitReport = async () => {
    if (!confirm('Kirim laporan ini? Data yang sudah dikirim tidak dapat diubah.')) return
    
    submitting.value = true
    try {
        await api.post('/api/reports/submit', {
            period_month: month.value,
            period_year: year.value,
            answers: answers.value
        })
        alert('Laporan berhasil terkirim!')
        router.push('/history')
    } catch (e: any) {
        alert(e.response?.data?.message || 'Gagal mengirim laporan.')
    } finally {
        submitting.value = false
    }
}
</script>

<template>
  <div class="px-5 py-8 max-w-lg mx-auto pb-24">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-900 leading-tight">Laporan Baru</h1>
        <p class="text-gray-500 text-sm mt-1">Isi formulir sesuai dengan kegiatan Anda.</p>
    </div>

    <div v-if="loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="h-32 bg-gray-200 animate-pulse rounded-2xl"></div>
    </div>

    <div v-else class="space-y-6">
        <!-- Periode Card -->
        <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-100">
            <h3 class="font-bold mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Periode Laporan
            </h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="text-xs font-bold text-indigo-200 uppercase mb-1 block">Tingkat/Bulan Hijri</label>
                    <select v-model="month" class="w-full bg-indigo-500 border-none rounded-xl text-white focus:ring-2 focus:ring-white py-3">
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                </div>
                <div class="hidden">
                     <label class="text-xs font-bold text-indigo-200 uppercase mb-1 block">Tahun Hijri</label>
                     <input v-model="year" type="number" class="w-full bg-indigo-500 border-none rounded-xl text-white focus:ring-2 focus:ring-white py-3" />
                </div>
            </div>
        </div>

        <form @submit.prevent="submitReport" class="space-y-6">
            <div v-for="cat in categories" :key="cat.id" class="space-y-4">
                <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest px-1">{{ cat.name }}</h2>
                
                <div v-for="q in cat.questions" :key="q.id" class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100">
                    <label class="block text-gray-800 font-semibold mb-3 leading-snug">
                        {{ q.question }}
                        <span v-if="q.is_required" class="text-red-500 ml-1">*</span>
                    </label>

                    <template v-if="q.type === 'text'">
                        <input v-model="answers[q.id]" :required="q.is_required" type="text" class="w-full bg-gray-50 border-gray-100 rounded-xl py-3 px-4 focus:border-green-500 focus:ring-green-500 transition" placeholder="Tulis jawaban...">
                    </template>

                    <template v-else-if="q.type === 'textarea'">
                        <textarea v-model="answers[q.id]" :required="q.is_required" class="w-full bg-gray-50 border-gray-100 rounded-xl py-3 px-4 focus:border-green-500 focus:ring-green-500 transition" rows="3" placeholder="Jelaskan detail..."></textarea>
                    </template>

                    <template v-else-if="q.type === 'select'">
                        <select v-model="answers[q.id]" :required="q.is_required" class="w-full bg-gray-50 border-gray-100 rounded-xl py-3 px-4 focus:border-green-500 focus:ring-green-500 transition">
                            <option value="">Pilih Opsi</option>
                            <option v-for="opt in q.options" :key="opt" :value="opt">{{ opt }}</option>
                        </select>
                    </template>

                    <template v-else-if="q.type === 'radio'">
                        <div class="space-y-2">
                             <label v-for="opt in q.options" :key="opt" class="flex items-center p-3 rounded-xl bg-gray-50 border border-transparent cursor-pointer active:bg-green-50 transition" :class="answers[q.id] === opt ? 'border-green-500 bg-green-50/30' : ''">
                                 <input type="radio" :name="'q_'+q.id" :value="opt" v-model="answers[q.id]" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                 <span class="ml-3 text-sm text-gray-700">{{ opt }}</span>
                             </label>
                        </div>
                    </template>
                </div>
            </div>

            <button type="submit" :disabled="submitting" class="w-full bg-green-600 text-white font-bold py-5 rounded-3xl shadow-xl shadow-green-100 active:scale-95 transition-all disabled:opacity-50 flex justify-center items-center">
                <span v-if="!submitting">Kirim Laporan</span>
                <span v-else class="flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sedang Mengirim...
                </span>
            </button>
        </form>
    </div>
  </div>
</template>
