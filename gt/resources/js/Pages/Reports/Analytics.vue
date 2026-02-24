<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps<{}>();

const type = ref('gt');
const month = ref(1);
const year = ref(Math.floor((new Date().getFullYear() - 622) * 33 / 32));
const questionId = ref('');

const categories = ref<any[]>([]);
const analytics = ref<any>(null);
const currentQuestion = ref<any>(null);
const loading = ref(false);

const fetchData = async () => {
    loading.value = true;
    try {
        let url = route('reports.analytics.data') + `?type=${type.value}&month=${month.value}&year=${year.value}`;
        if (questionId.value) {
            url += `&question_id=${questionId.value}`;
        }
        
        const res = await fetch(url);
        const data = await res.json();
        
        categories.value = data.categories;
        analytics.value = data.analytics;
        currentQuestion.value = data.question;
        
        // Auto-select first question if none selected and categories exist
        if (!questionId.value && data.categories.length > 0 && data.categories[0].questions.length > 0) {
            questionId.value = data.categories[0].questions[0].id;
        }
    } catch(e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

watch([type, month, year], () => {
    questionId.value = ''; // reset question on type change so it fetches the new list
    fetchData();
});

watch(questionId, (newVal, oldVal) => {
    if (newVal && newVal !== oldVal) {
        fetchData();
    }
});

onMounted(() => {
    fetchData();
});

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
];
const currentHijri = Math.floor((new Date().getFullYear() - 622) * 33 / 32);
const years = Array.from({length: 5}, (_, i) => currentHijri - 2 + i);

const getChartMax = (data: Record<string, number>) => {
    if(!data) return 1;
    let max = Math.max(...Object.values(data));
    return max > 0 ? max : 1;
};

</script>

<template>
    <Head title="Analitik Laporan" />
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-7xl mx-auto">
            
            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Analisa Data Laporan</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat rekap statistik jawaban atau daftar uraian text untuk setiap pertanyaan spesifik.</p>
                </div>
                <a :href="route('reports.index')" class="text-indigo-600 hover:underline text-sm font-semibold">
                    &larr; Kembali ke Daftar Laporan
                </a>
            </div>

            <!-- Dashboard Filters -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700 flex flex-wrap gap-4 items-center">
                 <select v-model="type" class="rounded-md border-gray-300 shadow-sm text-sm">
                      <option value="gt">Laporan GT</option>
                      <option value="pjgt">Laporan PJGT</option>
                      <option value="korwil">Laporan Korwil</option>
                 </select>
                 <select v-model="month" class="rounded-md border-gray-300 shadow-sm text-sm">
                      <option v-for="m in months" :value="m.value">{{ m.label }}</option>
                 </select>
                 <select v-model="year" class="rounded-md border-gray-300 shadow-sm text-sm">
                      <option v-for="y in years" :value="y">{{ y }}</option>
                 </select>
                 <div class="flex-1 min-w-[300px]">
                      <select v-model="questionId" class="w-full rounded-md border-gray-300 shadow-sm text-sm" :disabled="loading">
                           <option value="" disabled>-- Pilih Pertanyaan yang dianalisis --</option>
                           <optgroup v-for="cat in categories" :key="cat.id" :label="cat.name">
                                <option v-for="q in cat.questions" :key="q.id" :value="q.id">{{ q.question }}</option>
                           </optgroup>
                      </select>
                 </div>
            </div>

            <div v-if="loading" class="text-center p-12 text-gray-500">
                 Memuat data analitik...
            </div>

            <div v-else-if="!currentQuestion" class="bg-yellow-50 text-yellow-800 p-6 rounded-lg text-center font-medium border border-yellow-200">
                 Belum ada master pertanyaan untuk tipe laporan ini.
            </div>

            <div v-else class="space-y-6">
                 <!-- Analytics Card -->
                 <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                      <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200">
                           Hasil Tanggapan: <span class="text-indigo-600">{{ currentQuestion.question }}</span>
                      </h3>
                      
                      <!-- IF CHART DATA -->
                      <div v-if="analytics?.type === 'chart'" class="space-y-4">
                           <div v-if="Object.keys(analytics.data).length === 0" class="text-sm text-gray-500 italic">Belum ada jawaban untuk pertanyaan ini.</div>
                           <div v-for="(count, key) in analytics.data" :key="key" class="grid grid-cols-12 items-center gap-4">
                                <div class="col-span-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">
                                     {{ key }}
                                </div>
                                <div class="col-span-8 h-6 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden flex items-center shadow-inner relative">
                                     <div class="h-full bg-indigo-500 transition-all duration-500 relative" :style="`width: ${(count / getChartMax(analytics.data)) * 100}%`"></div>
                                </div>
                                <div class="col-span-1 text-sm font-bold text-gray-900 dark:text-gray-100">
                                     {{ count }}
                                </div>
                           </div>
                      </div>

                      <!-- IF LIST DATA -->
                      <div v-else-if="analytics?.type === 'list'">
                           <div v-if="analytics.data.length === 0" class="text-sm text-gray-500 italic text-center p-4">Belum ada tanggapan teks yang masuk.</div>
                           <div class="overflow-x-auto border rounded-xl" v-else>
                                <table class="w-full text-sm text-left">
                                     <thead class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-b">
                                          <tr>
                                               <th class="px-4 py-3 w-16">No</th>
                                               <th class="px-4 py-3 w-48">Pelapor</th>
                                               <th class="px-4 py-3">Konteks / Tempat</th>
                                               <th class="px-4 py-3 w-1/2">Isi Tanggapan/Kendala</th>
                                          </tr>
                                     </thead>
                                     <tbody>
                                          <tr v-for="(item, idx) in analytics.data" class="border-b last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700">
                                               <td class="px-4 py-3 text-center text-gray-500">{{ Number(idx) + 1 }}</td>
                                               <td class="px-4 py-3 font-semibold">{{ item.reporter }}</td>
                                               <td class="px-4 py-3 text-gray-600 text-[11px] leading-tight">{{ item.context }}</td>
                                               <td class="px-4 py-3">{{ item.answer }}</td>
                                          </tr>
                                     </tbody>
                                </table>
                           </div>
                      </div>
                 </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
