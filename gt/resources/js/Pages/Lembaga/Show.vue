<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

// Leaflet loaded async to avoid SSR issues
const LeafletMap = defineAsyncComponent(() => import('@/Components/LeafletMap.vue'));

const props = defineProps<{
    lembaga: {
        id: number;
        nama: string;
        alamat: string | null;
        latitude: string | null;
        longitude: string | null;
        urlmap: string | null;
        status: string;
        wilayah_id: number | null;
        pjgt_id: number | null;
        created_at: string;
        updated_at: string;
        wilayah?: { id: number; nama: string } | null;
        pjgt?: { id: number; nama: string; id_pjgt?: string; no_hp?: string } | null;
        kebutuhans?: any[];
        penugasans?: any[];
    };
    availableSkills: any[];
}>();

import { useForm } from '@inertiajs/vue3';

const formKeb = useForm({
    skill_id: '',
    prioritas: 'diutamakan',
    kuota: 1,
    keterangan: '',
});

const formKebVisible = ref(false);

const addKebutuhan = () => {
    formKeb.post(route('lembaga-kebutuhan.store', props.lembaga.id), {
        preserveScroll: true,
        onSuccess: () => {
            formKeb.reset();
            formKebVisible.value = false;
        },
    });
};

const removeKebutuhan = (keb: any) => {
    if (confirm(`Hapus kebutuhan skill '${keb.skill?.nama}' dari lembaga ini?`)) {
        router.delete(route('lembaga-kebutuhan.destroy', [props.lembaga.id, keb.id]), {
            preserveScroll: true,
        });
    }
};

const prioColor: Record<string, string> = {
    wajib:      'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    diutamakan: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    opsional:   'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
};

const confirmDelete = ref(false);
const doDelete = () => {
    router.delete(route('lembagas.destroy', props.lembaga.id), {
        onSuccess: () => confirmDelete.value = false,
    });
};

const hasCoords = props.lembaga.latitude && props.lembaga.longitude;
</script>

<template>
    <Head :title="`Detail – ${lembaga.nama}`" />
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-5xl">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('lembagas.index')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ lembaga.nama }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Detail Lembaga Penerima Guru Tugas</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('lembagas.edit', lembaga.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </Link>
                    <button
                        @click="confirmDelete = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                <!-- Left: Info Cards -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Status Badge -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Keaktifan</span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold"
                                :class="lembaga.status === 'aktif'
                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                    : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                            >
                                <span
                                    class="w-2 h-2 rounded-full"
                                    :class="lembaga.status === 'aktif' ? 'bg-emerald-500' : 'bg-red-500'"
                                />
                                {{ lembaga.status === 'aktif' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Info Detail -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Informasi Lembaga</h2>

                        <!-- Alamat -->
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Alamat</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ lembaga.alamat || '—' }}
                            </p>
                        </div>

                        <!-- Wilayah -->
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Wilayah</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ lembaga.wilayah?.nama || '—' }}
                            </p>
                        </div>

                        <!-- PJGT -->
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Koordinator PJGT</p>
                            <div v-if="lembaga.pjgt">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ lembaga.pjgt.nama }}</p>
                                <p v-if="lembaga.pjgt.id_pjgt" class="text-xs text-gray-400">
                                    ID: {{ lembaga.pjgt.id_pjgt }}
                                </p>
                                <p v-if="lembaga.pjgt.no_hp" class="text-xs text-gray-400">
                                    📞 {{ lembaga.pjgt.no_hp }}
                                </p>
                            </div>
                            <p v-else class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ditentukan</p>
                        </div>
                    </div>

                    <!-- Koordinat -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-3">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Data Lokasi</h2>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Latitude</p>
                                <p class="text-sm font-mono font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ lembaga.latitude || '—' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Longitude</p>
                                <p class="text-sm font-mono font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ lembaga.longitude || '—' }}
                                </p>
                            </div>
                        </div>

                        <div v-if="lembaga.urlmap">
                            <a
                                :href="lembaga.urlmap"
                                target="_blank"
                                class="inline-flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Buka di Google Maps
                            </a>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="text-xs text-gray-400 dark:text-gray-500 px-1 space-y-1">
                        <p>📅 Ditambahkan: {{ new Date(lembaga.created_at).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }) }}</p>
                        <p>🔄 Diperbarui: {{ new Date(lembaga.updated_at).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }) }}</p>
                    </div>
                </div>

                <!-- Right: Map + Kebutuhan -->
                <div class="lg:col-span-3 space-y-6">
                    
                    <!-- Panel: Kebutuhan Skill Lembaga -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 pb-3">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between mb-4">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">🧩 Kebutuhan Skill Guru Tugas</h2>
                            <button @click="formKebVisible = !formKebVisible" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                                {{ formKebVisible ? 'Tutup Form' : '+ Tambah Kebutuhan' }}
                            </button>
                        </div>
                        
                        <div class="px-5">
                            <!-- Form Tambah Kebutuhan -->
                            <div v-if="formKebVisible" class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3 border border-gray-100 dark:border-gray-600">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Skill/Keahlian</label>
                                        <select v-model="formKeb.skill_id" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm text-sm focus:ring-indigo-500 border-gray-300">
                                            <option value="" disabled>-- Pilih --</option>
                                            <option v-for="sk in availableSkills" :key="sk.id" :value="sk.id">{{ sk.nama }} ({{ sk.kategori }})</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritas</label>
                                        <select v-model="formKeb.prioritas" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm text-sm focus:ring-indigo-500 border-gray-300">
                                            <option value="wajib">Wajib (Harus ada)</option>
                                            <option value="diutamakan">Diutamakan (Sangat diharapkan)</option>
                                            <option value="opsional">Opsional (Nilai plus)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Kuota (Santri)</label>
                                        <input v-model="formKeb.kuota" type="number" min="1" max="99" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm text-sm border-gray-300">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Khusus (Opsional)</label>
                                        <input v-model="formKeb.keterangan" type="text" placeholder="Catatan untuk santri yang akan di-matching..." class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm text-sm border-gray-300">
                                    </div>
                                </div>
                                <div class="pt-2">
                                    <button @click="addKebutuhan" :disabled="formKeb.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                                        {{ formKeb.processing ? 'Menyimpan...' : 'Simpan Kebutuhan' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Daftar Kebutuhan -->
                            <div v-if="lembaga.kebutuhans && lembaga.kebutuhans.length > 0" class="space-y-2 mb-3">
                                <div v-for="keb in lembaga.kebutuhans" :key="keb.id" class="flex items-center justify-between p-3 border border-gray-100 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ keb.skill?.nama }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold uppercase rounded" :class="prioColor[keb.prioritas]">
                                                {{ keb.prioritas }}
                                            </span>
                                            <span class="text-xs text-gray-500 font-medium">Kuota: {{ keb.kuota }} GT</span>
                                            <span v-if="keb.keterangan" class="text-xs text-gray-400 italic hidden sm:inline-block">· {{ keb.keterangan }}</span>
                                        </div>
                                    </div>
                                    <button @click="removeKebutuhan(keb)" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Hapus Kebutuhan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-sm text-center py-6 text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl mb-3">
                                Lembaga ini belum memiliki kriteria kebutuhan riil Guru Tugas.
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">📍 Lokasi di Peta</h2>
                            <span v-if="hasCoords" class="text-xs text-gray-400">Klik marker untuk info</span>
                            <span v-else class="text-xs text-amber-500">Koordinat belum diset</span>
                        </div>

                        <div v-if="hasCoords" class="relative">
                            <Suspense>
                                <LeafletMap
                                    mode="view"
                                    :lat="lembaga.latitude"
                                    :lng="lembaga.longitude"
                                    height="420px"
                                />
                            </Suspense>
                        </div>

                        <!-- No coords placeholder -->
                        <div v-else class="flex flex-col items-center justify-center h-[420px] text-gray-400 dark:text-gray-500 gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <p class="text-sm font-medium">Koordinat belum tersedia</p>
                            <Link
                                :href="route('lembagas.edit', lembaga.id)"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline"
                            >
                                Edit dan tambahkan koordinat →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Penugasan Guru Tugas -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Riwayat Penempatan Guru Tugas</h2>
                
                <div v-if="lembaga.penugasans && lembaga.penugasans.length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode Tugas</th>
                                <th class="px-4 py-3 text-left">Nama Santri (GT)</th>
                                <th class="px-4 py-3 text-center">Periode / Tahun</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="tugas in lembaga.penugasans" :key="tugas.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-gray-600 dark:text-gray-300 font-medium">{{ tugas.kode_tugas || '—' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Link :href="route('santris.show', tugas.santri_id)" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ tugas.santri?.nama }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                    {{ tugas.tahun_psm?.judul ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full" 
                                        :class="tugas.status === 'diusulkan' ? 'bg-yellow-100 text-yellow-800' : 
                                                tugas.status === 'disetujui' ? 'bg-blue-100 text-blue-800' :
                                                tugas.status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 
                                                tugas.status === 'selesai' ? 'bg-gray-100 text-gray-700' : 
                                                'bg-red-100 text-red-700'">
                                        {{ tugas.status.charAt(0).toUpperCase() + tugas.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link :href="route('penugasans.show', tugas.id)" class="text-xs text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                                        Detail
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-6 text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    Belum ada riwayat penugasan untuk lembaga ini.
                </div>
            </div>

        </div>

        <ConfirmModal
            :show="confirmDelete"
            :message="`Hapus data lembaga '${lembaga.nama}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = false"
        />
    </AuthenticatedLayout>
</template>
