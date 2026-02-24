<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    report_type: 'gt',
    reporter_id: '',
    period_month: 1,
    period_year: Math.floor((new Date().getFullYear() - 622) * 33 / 32),
    santri_id: null as null|number,
    pjgt_id: null as null|number,
    lembaga_id: null as null|number,
});

const targets = ref<any[]>([]);
const availableContexts = ref<any[]>([]);
const selectedContextIndex = ref(-1);
const loadingTargets = ref(false);

const fetchTargets = async () => {
    loadingTargets.value = true;
    try {
        const res = await fetch(route('reports.targets') + '?type=' + form.report_type);
        const data = await res.json();
        targets.value = data;
        form.reporter_id = ''; 
        availableContexts.value = [];
        selectedContextIndex.value = -1;
    } catch (e) {
        console.error(e);
    } finally {
        loadingTargets.value = false;
    }
};

watch(() => form.report_type, () => {
    fetchTargets();
}, { immediate: true });

watch(() => form.reporter_id, (newVal) => {
    const target = targets.value.find(t => t.id === newVal);
    if (target && target.contexts && target.contexts.length > 0) {
        availableContexts.value = target.contexts;
        // Auto select if only 1 context
        if (target.contexts.length === 1) {
             selectedContextIndex.value = 0;
        } else {
             selectedContextIndex.value = -1;
        }
    } else {
        availableContexts.value = [];
        selectedContextIndex.value = -1;
    }
});

watch(selectedContextIndex, (index) => {
    if (index >= 0 && availableContexts.value[index]) {
         const ctx = availableContexts.value[index];
         form.santri_id = ctx.santri_id;
         form.pjgt_id = ctx.pjgt_id;
         form.lembaga_id = ctx.lembaga_id;
    } else {
         form.santri_id = null;
         form.pjgt_id = null;
         form.lembaga_id = null;
    }
});

const submit = () => {
    form.post(route('reports.init'));
};

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
</script>

<template>
    <Head title="Mulai Laporan Baru" />
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Mulai Pengisian Laporan (Manual/Draft)</h1>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel value="Tipe Laporan" />
                        <select v-model="form.report_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                            <option value="gt">Laporan Guru Tugas</option>
                            <option value="pjgt">Laporan PJGT</option>
                            <option value="korwil">Laporan Korwil</option>
                        </select>
                        <InputError :message="form.errors.report_type" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel value="Target Pelapor (Orang)" />
                        <select v-model="form.reporter_id" :disabled="loadingTargets || targets.length===0" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm disabled:opacity-50">
                            <option value="" disabled>-- Pilih Orang --</option>
                            <option v-for="t in targets" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                        <p v-if="loadingTargets" class="text-xs text-gray-500 mt-1">Memuat data...</p>
                        <InputError :message="form.errors.reporter_id" class="mt-2" />
                    </div>

                    <div v-if="availableContexts.length > 0">
                         <InputLabel value="Konteks Laporan (Tempat & Target Tugas)" />
                         <select v-model="selectedContextIndex" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                              <option :value="-1" disabled>-- Pilih Konteks Laporan --</option>
                              <option v-for="(ctx, idx) in availableContexts" :key="idx" :value="idx">
                                   GT: {{ ctx.santri_name }} | Lembaga: {{ ctx.lembaga_name }} {{ form.report_type==='pjgt' ? '' : '| PJGT: ' + ctx.pjgt_name }}
                              </option>
                         </select>
                         <p class="text-xs text-indigo-600 mt-1" v-if="availableContexts.length === 1">Konteks laporan dipilih otomatis karena hanya ada 1 penugasan aktif.</p>
                         <p class="text-xs font-semibold text-red-500 mt-1" v-if="availableContexts.length > 1 && selectedContextIndex === -1">Target/Konteks laporan belum dipilih!</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="Periode (Bulan)" />
                            <select v-model="form.period_month" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                                <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                            </select>
                            <InputError :message="form.errors.period_month" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel value="Periode (Tahun)" />
                            <select v-model="form.period_year" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                            <InputError :message="form.errors.period_year" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                        <PrimaryButton :class="{ 'opacity-25': form.processing || selectedContextIndex === -1 }" :disabled="form.processing || selectedContextIndex === -1">
                            Lanjutkan ke Form &rarr;
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
