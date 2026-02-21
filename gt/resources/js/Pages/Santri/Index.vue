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
        route('santris.index'),
        { search: value },
        { preserveState: true, replace: true }
    );
}, 300));

const columns = [
    { key: 'foto',          label: 'Foto' },
    { key: 'nama',          label: 'Nama Lengkap' },
    { key: 'nis',           label: 'NIS' },
    { key: 'jenis_kelamin', label: 'Gender' },
    { key: 'kelas',         label: 'Kelas' },
    { key: 'angkatan',      label: 'Angkatan' },
    { key: 'status_tugas',  label: 'Status Tugas' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('santris.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};

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
</script>

<template>
    <Head title="Data Santri" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Data Santri (Guru Tugas)</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data santri dan status penugasannya.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="santris.data"
                create-route="santris.create"
                create-label="Tambah"
                edit-route="santris.edit"
                delete-route="santris.destroy"
                :can-create="true"
                :can-edit="true"
                :can-delete="true"
                @delete="confirmDelete = $event"
            >
                <template #filters>
                    <div class="flex flex-col sm:flex-row gap-2 w-full">
                        <div class="w-full sm:max-w-xs relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari santri..."
                                class="pl-10 input dark:bg-gray-800 dark:text-white dark:border-gray-700 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
                            />
                        </div>
                        <div class="flex gap-2">
                            <Link :href="route('santris.trash')" class="btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 inline-flex items-center gap-2 px-3 py-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Tong Sampah
                            </Link>
                            <a :href="route('santris.export')" class="btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 inline-flex items-center gap-2 px-3 py-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Export
                            </a>
                            <label class="btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 inline-flex items-center gap-2 px-3 py-2 text-sm cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Import
                                <input type="file" class="hidden" @change="handleImport" accept=".xlsx,.csv" />
                            </label>
                        </div>
                    </div>
                </template>
                <template #foto="{ row }">
                    <Link :href="route('santris.show', row.id)">
                        <img v-if="row.foto" :src="'/storage/' + row.foto" class="h-10 w-10 rounded-full object-cover border border-gray-200 hover:ring-2 hover:ring-indigo-400 transition-all" alt="Foto" />
                        <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold">
                            {{ row.nama?.charAt(0)?.toUpperCase() }}
                        </div>
                    </Link>
                </template>
                <template #nama="{ row }">
                    <div>
                        <Link :href="route('santris.show', row.id)" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ row.nama }}
                        </Link>
                        <p v-if="row.nama_ayah" class="text-xs text-gray-400 mt-0.5">Ayah: {{ row.nama_ayah }}</p>
                    </div>
                </template>
                <template #status_tugas="{ row }">
                    <span
                        class="inline-block px-2 py-0.5 text-xs rounded-full font-medium"
                        :class="row.status_tugas === 'Menunggu' ? 'bg-yellow-50 text-yellow-700' :
                                row.status_tugas === 'Sedang Bertugas' ? 'bg-blue-50 text-blue-700' :
                                'bg-green-50 text-green-700'"
                    >{{ row.status_tugas }}</span>
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-3">
                        <Link :href="route('santris.show', row.id)" class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 text-sm font-medium">
                            Detail
                        </Link>
                        <Link :href="route('santris.edit', row.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                            Edit
                        </Link>
                        <button @click="$emit('delete', row)" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                            Delete
                        </button>
                    </div>
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
            :message="`Hapus data santri '${confirmDelete?.nama}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
