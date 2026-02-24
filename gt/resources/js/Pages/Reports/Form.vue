<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps<{
    report: any;
    categories: any[];
    existingAnswers: Record<string, any>;
}>();

// Initialize the form state mapping all question IDs
const initAnswers = () => {
    let answers: Record<string, any> = {};
    props.categories.forEach(cat => {
        cat.questions.forEach((q: any) => {
             // Let see if there's an existing answer
             let val = props.existingAnswers[q.id];
             
             if (q.type === 'checkbox') {
                 try {
                     // Checkbox should be an array
                     val = val ? JSON.parse(val) : [];
                 } catch (e) { val = []; }
             } else {
                 val = val || '';
             }
             
             answers[q.id] = val;
        });
    });
    return answers;
};

const form = useForm({
    answers: initAnswers(),
    status: props.report.status || 'draft',
});

const saveDraft = () => {
    form.status = 'draft';
    form.put(route('reports.update', props.report.id), {
        preserveScroll: true,
    });
};

const submitFormal = () => {
    if (confirm('Yakin ingin menyimpan laporan ini secara permanen?')) {
        form.status = 'submitted';
        form.put(route('reports.update', props.report.id));
    }
};

const setEvaluated = () => {
    if (confirm('Tandai laporan ini telah dievaluasi?')) {
        form.status = 'evaluated';
        form.put(route('reports.update', props.report.id));
    }
};

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
    ];
    const found = months.find(m => m.value === val);
    return found ? found.label : (val || '-');
};

</script>

<template>
    <Head title="Isi Laporan" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6 pb-20">
            <div class="flex items-center justify-between mb-6">
                <div>
                   <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                      Formulir Laporan <span class="uppercase text-indigo-600">({{ report.report_type }})</span>
                   </h1>
                   <div class="mt-2 text-sm text-gray-500 space-y-1">
                        <p>Pelapor: <span class="font-semibold text-gray-800">{{ report.reporter_name }}</span> | Periode: {{ getPeriodLabel(report.period_month) }} {{ report.period_year }} H</p>
                        <p v-if="report.lembaga_id">Tempat/Lembaga Tugas: <span class="font-semibold">{{ report.lembaga?.nama }}</span></p>
                        <p v-if="report.report_type!=='pjgt' && report.pjgt_id">PJGT Lembaga: <span class="font-semibold">{{ report.pjgt?.nama }}</span></p>
                        <p v-if="report.report_type!=='gt' && report.santri_id">Laporan Atas Target GT: <span class="font-semibold">{{ report.santri?.nama }}</span></p>
                   </div>
                </div>
                <div>
                    <span class="px-3 py-1 rounded shadow-sm text-sm uppercase font-bold"
                          :class="{
                              'bg-yellow-100 text-yellow-800': form.status === 'draft',
                              'bg-green-100 text-green-800': form.status === 'submitted',
                              'bg-blue-100 text-blue-800': form.status === 'evaluated',
                          }">
                        Status: {{ form.status === 'submitted' ? 'Terkirim' : (form.status === 'evaluated' ? 'Dievaluasi' : 'Draft') }}
                    </span>
                </div>
            </div>

            <div v-if="categories.length === 0" class="text-center py-10 bg-white rounded-lg shadow!">
                 Belum ada master kategori/pertanyaan untuk tipe laporan ini.
            </div>

            <form @submit.prevent>
            <div v-for="cat in categories" :key="cat.id" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 mb-6">
                 <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                     <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ cat.name }}</h3>
                 </div>
                 
                 <div class="p-6 space-y-6">
                     <div v-for="(q, idx) in cat.questions" :key="q.id" class="space-y-2">
                         <InputLabel>
                             <span class="font-bold mr-1">{{ Number(idx) + 1 }}.</span> {{ q.question }}
                             <span v-if="q.is_required" class="text-red-500 font-bold ml-1">*</span>
                         </InputLabel>

                         <!-- Input Type Selection -->
                         <template v-if="q.type === 'text'">
                              <TextInput v-model="form.answers[q.id]" class="w-full" :required="q.is_required" />
                         </template>

                         <template v-else-if="q.type === 'textarea'">
                              <textarea v-model="form.answers[q.id]" rows="4" :required="q.is_required" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"></textarea>
                         </template>
                         
                         <template v-else-if="q.type === 'select'">
                              <select v-model="form.answers[q.id]" :required="q.is_required" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                                   <option value="" disabled>-- Pilih Salah Satu --</option>
                                   <option v-for="opt in q.options" :key="opt" :value="opt">{{ opt }}</option>
                              </select>
                         </template>

                         <template v-else-if="q.type === 'radio'">
                              <div class="flex flex-col space-y-2 mt-2">
                                  <label v-for="opt in q.options" :key="opt" class="flex items-center space-x-2 cursor-pointer">
                                       <input type="radio" :name="'q_' + q.id" :value="opt" v-model="form.answers[q.id]" :required="q.is_required && !form.answers[q.id]" class="rounded-full border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" />
                                       <span class="text-sm text-gray-700 dark:text-gray-300">{{ opt }}</span>
                                  </label>
                              </div>
                         </template>

                         <template v-else-if="q.type === 'checkbox'">
                              <div class="flex flex-col space-y-2 mt-2">
                                  <label v-for="opt in q.options" :key="opt" class="flex items-center space-x-2 cursor-pointer">
                                       <input type="checkbox" :value="opt" v-model="form.answers[q.id]" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" />
                                       <span class="text-sm text-gray-700 dark:text-gray-300">{{ opt }}</span>
                                  </label>
                              </div>
                         </template>
                     </div>
                 </div>
            </div>

            <!-- Action Floating Area -->
            <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-lg z-10 sm:left-64 flex justify-between">
                <div class="text-sm text-gray-500 flex items-center">
                    Simpan proses sebagai draft jika belum selesai.
                </div>
                <div class="flex space-x-3">
                    <SecondaryButton v-if="form.status !== 'evaluated'" @click="saveDraft" :disabled="form.processing">Simpan Draft</SecondaryButton>
                    <PrimaryButton v-if="form.status === 'draft'" @click="submitFormal" :disabled="form.processing">Kirim Laporan Valid</PrimaryButton>
                    <PrimaryButton v-if="form.status === 'submitted'" @click="setEvaluated" :disabled="form.processing" class="!bg-blue-600 hover:!bg-blue-700">Tandai Telah Dievaluasi</PrimaryButton>
                    <span v-if="form.status === 'evaluated'" class="text-blue-600 font-bold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        Laporan telah disetujui & dievaluasi.
                    </span>
                </div>
            </div>
            </form>

        </div>
    </AuthenticatedLayout>
</template>
