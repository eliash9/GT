<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    penugasans: { data: any[]; links: any[] };
    filters?:   { search?: string; status?: string; wilayah_id?: string };
    wilayahs:   any[];
    statuses:   string[];
}>();

const search    = ref(props.filters?.search ?? '');
const status    = ref(props.filters?.status ?? '');
const wilayahId = ref(props.filters?.wilayah_id ?? '');

const doFilter = debounce(() => {
    router.get(route('penugasans.index'), {
        search: search.value, status: status.value, wilayah_id: wilayahId.value,
    }, { preserveState: true, replace: true });
}, 300);

watch([search, status, wilayahId], doFilter);

const confirmDelete = ref<any>(null);

const doDelete = () => {
    if (confirmDelete.value) {
        router.delete(route('penugasans.destroy', confirmDelete.value.id), {
            onSuccess: () => (confirmDelete.value = null),
        });
    }
};

const statusColor: Record<string, string> = {
    diusulkan:  'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    disetujui:  'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    aktif:      'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    selesai:    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
    dibatalkan: 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400',
};

const labelStatus: Record<string, string> = {
    diusulkan: 'Diusulkan', disetujui: 'Disetujui', aktif: 'Aktif',
    selesai: 'Selesai', dibatalkan: 'Dibatalkan',
};
</script>

<template>
    <Head title="Data Penugasan" />
    <AuthenticatedLayout>
        <div class="space-y-5">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Data Penugasan</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola penugasan guru tugas ke lembaga penerima.</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('matching.index')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                        🎯 Matching Otomatis
                    </Link>
                    <Link :href="route('penugasans.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Manual
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="search" type="text" placeholder="Cari nama santri / lembaga..."
                        class="pl-10 input block w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm" />
                </div>
                <select v-model="status" class="input rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm">
                    <option value="">Semua Status</option>
                    <option v-for="s in statuses" :key="s" :value="s">{{ labelStatus[s] }}</option>
                </select>
                <select v-model="wilayahId" class="input rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm">
                    <option value="">Semua Wilayah</option>
                    <option v-for="w in wilayahs" :key="w.id" :value="String(w.id)">{{ w.nama }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left">Santri & Kode</th>
                                <th class="px-5 py-3 text-left">Lembaga & PJGT</th>
                                <th class="px-5 py-3 text-left">Wilayah</th>
                                <th class="px-5 py-3 text-center">Skor</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-center">Tahun PSM</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="penugasans.data.length === 0">
                                <td colspan="7" class="text-center py-12 text-gray-400">Belum ada data penugasan.</td>
                            </tr>
                            <tr v-for="p in penugasans.data" :key="p.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3">
                                    <Link :href="route('penugasans.show', p.id)" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ p.santri?.nama }}
                                    </Link>
                                    <p v-if="p.kode_tugas" class="text-xs font-mono font-medium text-gray-500 mt-0.5">#{{ p.kode_tugas }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ p.lembaga?.nama }}</span>
                                    <p class="text-xs text-gray-400 mt-0.5">👤 {{ p.lembaga?.pjgt?.nama ?? p.lembaga?.wilayah?.pjgt?.nama ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ p.lembaga?.wilayah?.nama ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ p.skor_kecocokan }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full" :class="statusColor[p.status]">
                                        {{ labelStatus[p.status] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center text-gray-500 dark:text-gray-400 text-xs">
                                    {{ p.tahun_psm?.judul ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('penugasans.show', p.id)"
                                            class="text-xs text-gray-500 hover:text-indigo-600 dark:text-gray-400 font-medium">Detail</Link>
                                        <Link :href="route('penugasans.edit', p.id)"
                                            class="text-xs text-gray-500 hover:text-indigo-600 dark:text-gray-400 font-medium">Edit</Link>
                                        <button @click="confirmDelete = p"
                                            class="text-xs text-red-400 hover:text-red-600 font-medium">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end" v-if="penugasans.links && penugasans.links.length > 3">
                <component v-for="link in penugasans.links" :key="link.label"
                    :is="link.url ? Link : 'span'" :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border dark:border-gray-700"
                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : link.url ? 'border-gray-300 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' : 'border-gray-200 text-gray-400 cursor-not-allowed'" />
            </div>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :title="'Hapus Penugasan'"
            :message="`Hapus penugasan '${confirmDelete?.santri?.nama}' di '${confirmDelete?.lembaga?.nama}'?`"
            :confirmText="'Ya, Hapus'"
            :cancelText="'Batal'"
            :type="'danger'"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
