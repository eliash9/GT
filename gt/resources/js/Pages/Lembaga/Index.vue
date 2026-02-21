<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    lembagas: { data: any[]; links: any[] };
    filters?: { search?: string };
}>();

const search = ref(props.filters?.search ?? '');

watch(search, debounce((value) => {
    router.get(
        route('lembagas.index'),
        { search: value },
        { preserveState: true, replace: true }
    );
}, 300));

const columns = [
    { key: 'nama',    label: 'Nama Lembaga' },
    { key: 'alamat',  label: 'Alamat' },
    { key: 'wilayah', label: 'Wilayah' },
    { key: 'pjgt',    label: 'PJGT' },
    { key: 'status',  label: 'Status' },
    { key: 'lokasi',  label: 'Lokasi' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('lembagas.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};

const handleImport = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        router.post(route('lembagas.import'), { file: target.files[0] }, {
            forceFormData: true,
            onFinish: () => { target.value = ''; }
        });
    }
};
</script>

<template>
    <Head title="Data Lembaga" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Data Lembaga Penerima</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data lembaga penerima Guru Tugas.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="lembagas.data"
                create-route="lembagas.create"
                create-label="Tambah"
                edit-route="lembagas.edit"
                delete-route="lembagas.destroy"
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
                                placeholder="Cari lembaga / alamat..."
                                class="pl-10 input dark:bg-gray-800 dark:text-white dark:border-gray-700 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
                            />
                        </div>
                        <div class="flex gap-2">
                            <!-- Peta Sebaran -->
                            <Link :href="route('lembagas.sebaran')"
                                class="btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 inline-flex items-center gap-2 px-3 py-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Peta Sebaran
                            </Link>
                            <a :href="route('lembagas.export')"
                                class="btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 inline-flex items-center gap-2 px-3 py-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export
                            </a>
                            <label class="btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 inline-flex items-center gap-2 px-3 py-2 text-sm cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import
                                <input type="file" class="hidden" @change="handleImport" accept=".xlsx,.csv" />
                            </label>
                        </div>
                    </div>
                </template>

                <!-- Nama — clickable ke show -->
                <template #nama="{ row }">
                    <Link :href="route('lembagas.show', row.id)"
                        class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        {{ row.nama }}
                    </Link>
                </template>

                <template #wilayah="{ row }">
                    <span v-if="row.wilayah">{{ row.wilayah.nama }}</span>
                    <span v-else class="text-gray-400 italic">N/A</span>
                </template>

                <template #pjgt="{ row }">
                    <span v-if="row.pjgt">{{ row.pjgt.nama }}</span>
                    <span v-if="row.pjgt && row.pjgt.id_pjgt" class="text-xs text-gray-500 block">ID: {{ row.pjgt.id_pjgt }}</span>
                    <span v-else-if="!row.pjgt" class="text-gray-400 italic">N/A</span>
                </template>

                <template #status="{ row }">
                    <span
                        class="inline-block px-2 py-0.5 text-xs rounded-full font-medium"
                        :class="row.status === 'aktif' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                    >{{ row.status === 'aktif' ? 'Aktif' : 'Non-Aktif' }}</span>
                </template>

                <!-- Lokasi icon -->
                <template #lokasi="{ row }">
                    <span v-if="row.latitude && row.longitude">
                        <Link :href="route('lembagas.show', row.id)"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Lihat
                        </Link>
                    </span>
                    <span v-else class="text-xs text-gray-400 italic">—</span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end mt-4" v-if="lembagas.links && lembagas.links.length > 3">
                <component
                    v-for="link in lembagas.links"
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
            :message="`Hapus data Lembaga '${confirmDelete?.nama}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
