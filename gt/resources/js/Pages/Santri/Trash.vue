<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    santris: { data: any[]; links: any[] };
    filters?: { search?: string };
}>();

const search = ref(props.filters?.search ?? '');

watch(search, debounce((value) => {
    router.get(
        route('santris.trash'),
        { search: value },
        { preserveState: true, replace: true }
    );
}, 300));

const columns = [
    { key: 'foto',          label: 'Foto' },
    { key: 'nis',           label: 'NIS' },
    { key: 'nama',          label: 'Nama Lengkap' },
    { key: 'jenis_kelamin', label: 'Gender' },
    { key: 'kelas',         label: 'Kelas' },
    { key: 'nama_ayah',     label: 'Nama Ayah' },
    { key: 'angkatan',      label: 'Angkatan' },
    { key: 'status_tugas',  label: 'Status Tugas' },
];

const handleImport = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        router.post(route('santris.import'), { file: target.files[0] }, {
            forceFormData: true,
            onFinish: () => {
                target.value = '';
            }
        });
    }
};

const restoreItem = ref<any>(null);
const doRestore = () => {
    router.post(route('santris.restore', restoreItem.value.id), {}, {
        onSuccess: () => restoreItem.value = null,
    });
};

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('santris.force-delete', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};
</script>

<template>
    <Head title="Data Santri" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tong Sampah Santri</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data santri yang sudah dihapus sementara.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="santris.data"
                empty-text="Tidak ada data di tong sampah."
                :can-create="false"
                :can-edit="false"
                :can-delete="true"
                @delete="confirmDelete = $event"
            >
                <template #actions="{ row }">
                    <button
                        @click="restoreItem = row"
                        class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 text-sm font-medium"
                    >
                        Pulihkan
                    </button>
                    <button
                        @click="confirmDelete = row"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium"
                    >
                        Hapus Permanen
                    </button>
                </template>
                <template #filters>
                    <div class="flex flex-col sm:flex-row gap-2 justify-between w-full">
                        <div class="w-full sm:max-w-xs relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari santri dihapus..."
                                class="pl-10 input dark:bg-gray-800 dark:text-white dark:border-gray-700 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
                            />
                        </div>
                        <div class="flex gap-2">
                            <Link :href="route('santris.index')" class="btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 inline-flex items-center gap-2 px-3 py-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Kembali ke Data Santri
                            </Link>
                        </div>
                    </div>
                </template>
                <template #foto="{ row }">
                    <img v-if="row.foto" :src="'/storage/' + row.foto" class="h-10 w-10 rounded-full object-cover border border-gray-200" alt="Foto" />
                    <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">N/A</div>
                </template>
                <template #status_tugas="{ row }">
                    <span
                        class="inline-block px-2 py-0.5 text-xs rounded-full font-medium"
                        :class="row.status_tugas === 'Menunggu' ? 'bg-yellow-50 text-yellow-700' :
                                row.status_tugas === 'Sedang Bertugas' ? 'bg-blue-50 text-blue-700' :
                                'bg-green-50 text-green-700'"
                    >{{ row.status_tugas }}</span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end mt-4" v-if="santris.links && santris.links.length > 3">
                <component
                    v-for="link in santris.links"
                    :key="link.label"
                    :is="link.url ? Link : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border dark:border-gray-700"
                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : link.url ? 'border-gray-300 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' : 'border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'"
                />
            </div>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :message="`Hapus PERMANEN data santri '${confirmDelete?.nama}'? Data tidak bisa dikembalikan.`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />

        <ConfirmModal
            :show="!!restoreItem"
            :message="`Pulihkan data santri '${restoreItem?.nama}'?`"
            @confirm="doRestore"
            @cancel="restoreItem = null"
        />
    </AuthenticatedLayout>
</template>
