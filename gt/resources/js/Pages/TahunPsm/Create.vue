<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import moment from 'moment-hijri';

// Set locale Indonesia untuk nama bulan hijriah (opsional, jika moment-hijri tidak full id, kita bisa translate sendiri. Moment-hijri default-nya nama bulan standar)
moment.locale('id');

const form = useForm({
    judul: '',
    tanggal_mulai_masehi: '',
    tanggal_selesai_masehi: '',
    tanggal_mulai_hijriah: '',
    tanggal_selesai_hijriah: '',
    aktif: false,
});

const getHijriDate = (dateString: string) => {
    if (!dateString) return '';
    const m = moment(dateString, 'YYYY-MM-DD');
    if (!m.isValid()) return '';
    
    // moment-hijri format: iD iMMMM iYYYY
    // Karena lokalisasi iMMMM mungkin tetap bahasa Arab/Inggris, mari kita mapping manual nama bulan
    const hijriMonths = [
        'Muharram', 'Safar', 'Rabiul Awwal', 'Rabiul Akhir', 'Jumadil Awwal', 'Jumadil Akhir',
        'Rajab', 'Sya\'ban', 'Ramadhan', 'Syawal', 'Dzulqa\'dah', 'Dzulhijjah'
    ];
    
    const iD = m.iDate();
    const iM = m.iMonth(); // 0-11
    const iY = m.iYear();
    
    return `${iD} ${hijriMonths[iM]} ${iY}`;
};

watch(() => form.tanggal_mulai_masehi, (val) => {
    if (val) {
        form.tanggal_mulai_hijriah = getHijriDate(val);
    } else {
        form.tanggal_mulai_hijriah = '';
    }
});

watch(() => form.tanggal_selesai_masehi, (val) => {
    if (val) {
        form.tanggal_selesai_hijriah = getHijriDate(val);
    } else {
        form.tanggal_selesai_hijriah = '';
    }
});

const submit = () => {
    form.post(route('tahun-psm.store'));
};
</script>

<template>
    <Head title="Tambah Tahun PSM" />
    <AuthenticatedLayout>
        <div class="max-w-2xl space-y-5">
            <div class="flex items-center gap-3">
                <Link :href="route('tahun-psm.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Tahun PSM</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Master data periode penugasan GT.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Tahun / Periode <span class="text-red-500">*</span></label>
                    <input v-model="form.judul" type="text" placeholder="Contoh: 1445-1446 H / 2024-2025 M"
                        class="input block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required />
                    <p v-if="form.errors.judul" class="text-xs text-red-500 mt-1">{{ form.errors.judul }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai (Masehi)</label>
                        <input v-model="form.tanggal_mulai_masehi" type="date"
                            class="input block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        <p v-if="form.errors.tanggal_mulai_masehi" class="text-xs text-red-500 mt-1">{{ form.errors.tanggal_mulai_masehi }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai (Hijriah)</label>
                        <input v-model="form.tanggal_mulai_hijriah" type="text" placeholder="Contoh: 1 Syawal 1445"
                            class="input block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai (Masehi)</label>
                        <input v-model="form.tanggal_selesai_masehi" type="date"
                            class="input block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        <p v-if="form.errors.tanggal_selesai_masehi" class="text-xs text-red-500 mt-1">{{ form.errors.tanggal_selesai_masehi }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai (Hijriah)</label>
                        <input v-model="form.tanggal_selesai_hijriah" type="text" placeholder="Contoh: 10 Ramadhan 1446"
                            class="input block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" v-model="form.aktif" class="sr-only peer" />
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Jadikan Periode Aktif</span>
                            <p class="text-xs text-gray-500 mt-0.5">Tahun PSM aktif akan jadi default pada penugasan baru.</p>
                        </div>
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                    <button type="submit" :disabled="form.processing"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg transition-colors">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Tahun PSM' }}
                    </button>
                    <Link :href="route('tahun-psm.index')" class="px-5 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium text-sm">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
