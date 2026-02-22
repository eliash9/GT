<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { debounce } from 'lodash';

// Definisikan props dari SeleksiController@index
const props = defineProps<{
    santris: any[];
    filters: { search?: string; status_seleksi?: string };
}>();

// State filter
const search = ref(props.filters.search || '');
const statusSeleksi = ref(props.filters.status_seleksi || '');

// Form mass update
const form = useForm({
    santris: props.santris.map(s => ({
        id: s.id,
        nama: s.nama,
        nis: s.nis,
        kelas_formal: s.kelas_formal,
        status_kelulusan: s.status_kelulusan || 'Belum Lulus',
        status_seleksi: s.status_seleksi || 'Belum Diproses',
        akademisi: s.akademisi || '',
        marhalah_alquran: s.marhalah_alquran || '',
        haliyah_keaktifan: s.haliyah_keaktifan || '',
    }))
});

// Watcher untuk Filter => GET route (Reload Inertia)
watch([search, statusSeleksi], debounce(([s, status]) => {
    router.get(
        route('seleksi.index'),
        { search: s, status_seleksi: status },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}, 300));

// Watch props: saat data berubah karena load atau filter, perbarui form list
watch(() => props.santris, (newVal) => {
    form.santris = newVal.map(s => ({
        id: s.id,
        nama: s.nama,
        nis: s.nis,
        kelas_formal: s.kelas_formal,
        status_kelulusan: s.status_kelulusan || 'Belum Lulus',
        status_seleksi: s.status_seleksi || 'Belum Diproses',
        akademisi: s.akademisi || '',
        marhalah_alquran: s.marhalah_alquran || '',
        haliyah_keaktifan: s.haliyah_keaktifan || '',
    }));
});

const submit = () => {
    form.post(route('seleksi.mass-update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Sukses
        }
    });
};

const totalLolosAkhir = computed(() => form.santris.filter(s => s.status_seleksi === 'Lolos Tahap Akhir').length);
</script>

<template>
    <Head title="Seleksi Cepat & Massal" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Seleksi Cepat / Massal Santri GT
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
                
                <!-- Filter Kiri -->
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-2/3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama, NIS, atau kelas..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        />
                    </div>
                </div>

                <!-- Simpan Button -->
                <div class="w-full md:w-auto mt-4 md:mt-0 flex gap-4 shrink-0 items-center justify-end">
                    <span class="text-sm text-gray-500">
                        Memilih: <strong class="text-indigo-600 dark:text-indigo-400">{{ totalLolosAkhir }}</strong> lolos akhir
                    </span>
                    <button
                        @click="submit"
                        :disabled="form.processing || form.santris.length === 0"
                        class="inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Simpan Massal
                    </button>
                    <Link
                        :href="route('seleksi.rekap')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        Lihat Rekap
                    </Link>
                </div>
            </div>

            <!-- Tabel Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div v-if="form.santris.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelulusan</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-44">Status Seleksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="(item, index) in form.santris" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/80 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ item.nama }}</div>
                                    <div class="text-xs text-gray-500">NIS: {{ item.nis }} | Kelas: {{ item.kelas_formal || '—' }}</div>
                                </td>
                                
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <select
                                        v-model="item.status_kelulusan"
                                        @change="(item.status_kelulusan !== 'Lulus' && item.status_seleksi === 'Lolos Tahap Akhir') ? item.status_seleksi = 'Belum Diproses' : null"
                                        class="block w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="Belum Lulus">Belum Lulus</option>
                                        <option value="Lulus">Lulus</option>
                                        <option value="Tidak Lulus">Tidak Lulus</option>
                                    </select>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <select
                                        v-model="item.status_seleksi"
                                        class="block w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        :class="{
                                            'border-green-500 text-green-700 bg-green-50': item.status_seleksi === 'Lolos Tahap Akhir',
                                            'border-red-500 text-red-700 bg-red-50': item.status_seleksi === 'Tidak Lolos',
                                        }"
                                    >
                                        <option value="Belum Diproses">Belum Diproses</option>
                                        <option value="Lolos Tahap Awal">Lolos Tahap Awal</option>
                                        <option value="Lolos Tahap Akhir" :disabled="item.status_kelulusan !== 'Lulus'">Lolos Tahap Akhir</option>
                                        <option value="Tidak Lolos">Tidak Lolos</option>
                                    </select>
                                    <div v-if="item.status_kelulusan !== 'Lulus' && item.status_seleksi === 'Lolos Tahap Akhir'" class="text-[10px] text-red-500 mt-1">
                                        Wajib lulus untuk posisi ini!
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada santri</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pencarian tidak menemukan hasil atau tidak ada santri yang sesuai kriteria.</p>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
