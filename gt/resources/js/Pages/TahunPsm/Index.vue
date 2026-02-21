<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    tahunPsms: { data: any[]; links: any[] };
    filters?: { search?: string };
}>();

const formatDate = (dateString: string) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const search = ref(props.filters?.search ?? '');

const doSearch = debounce(() => {
    router.get(route('tahun-psm.index'), { search: search.value }, { preserveState: true, replace: true });
}, 300);

watch(search, doSearch);

const confirmDelete = ref<any>(null);

const doDelete = () => {
    if (confirmDelete.value) {
        router.delete(route('tahun-psm.destroy', confirmDelete.value.id), {
            onSuccess: () => (confirmDelete.value = null),
        });
    }
};
</script>

<template>
    <Head title="Master Tahun PSM" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Master Tahun PSM</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola periode penugasan GT (Masehi & Hijriah).</p>
                </div>
                <Link :href="route('tahun-psm.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Tahun PSM
                </Link>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="relative w-full sm:w-72">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input v-model="search" type="text" placeholder="Cari Judul..."
                            class="pl-10 input block w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Tahun / Judul PSM</th>
                                <th class="px-6 py-3">Masehi</th>
                                <th class="px-6 py-3">Hijriah</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="tahunPsms.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-gray-400">Belum ada data Tahun PSM.</td>
                            </tr>
                            <tr v-for="t in tahunPsms.data" :key="t.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ t.judul }}</p>
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    <p class="text-xs text-gray-500">Mulai: <span class="text-gray-800 dark:text-gray-200">{{ formatDate(t.tanggal_mulai_masehi) }}</span></p>
                                    <p class="text-xs text-gray-500">Selesai: <span class="text-gray-800 dark:text-gray-200">{{ formatDate(t.tanggal_selesai_masehi) }}</span></p>
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    <p class="text-xs text-gray-500">Mulai: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ t.tanggal_mulai_hijriah ?? '—' }}</span></p>
                                    <p class="text-xs text-gray-500">Selesai: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ t.tanggal_selesai_hijriah ?? '—' }}</span></p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span v-if="t.aktif" class="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-semibold rounded-full border border-emerald-100 dark:border-emerald-800">
                                        Aktif
                                    </span>
                                    <span v-else class="text-xs text-gray-400">Non-Aktif</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <Link :href="route('tahun-psm.edit', t.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-xs">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete = t" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium text-xs">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="tahunPsms.links.length > 3" class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-1">
                    <template v-for="(link, k) in tahunPsms.links" :key="k">
                        <component
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1.5 text-sm rounded-lg border"
                            :class="[
                                link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700',
                                !link.url && !link.active ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        />
                    </template>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :title="'Hapus Tahun PSM'"
            :message="`Hapus Tahun PSM '${confirmDelete?.judul}'?`"
            :confirmText="'Ya, Hapus'"
            :cancelText="'Batal'"
            :type="'danger'"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
