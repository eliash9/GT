<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    wilayahs: { data: any[]; links: any[] };
    filters?: { search?: string };
}>();

const search = ref(props.filters?.search ?? '');

watch(search, debounce((value) => {
    router.get(
        route('wilayahs.index'),
        { search: value },
        { preserveState: true, replace: true }
    );
}, 300));

const columns = [
    { key: 'nama', label: 'Nama Wilayah' },
    { key: 'pjgt', label: 'Penanggung Jawab (PJGT)' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('wilayahs.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};
</script>

<template>
    <Head title="Data Wilayah" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Data Wilayah</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data pembagian wilayah.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="wilayahs.data"
                create-route="wilayahs.create"
                create-label="Tambah"
                edit-route="wilayahs.edit"
                delete-route="wilayahs.destroy"
                :can-create="true"
                :can-edit="true"
                :can-delete="true"
                @delete="confirmDelete = $event"
            >
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
                                placeholder="Cari wilayah..."
                                class="pl-10 input dark:bg-gray-800 dark:text-white dark:border-gray-700 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
                            />
                        </div>
                    </div>
                </template>

                <template #pjgt="{ row }">
                    <span v-if="row.pjgt">{{ row.pjgt.nama }}</span>
                    <span v-if="row.pjgt && row.pjgt.id_pjgt" class="text-xs text-gray-500 block">ID: {{ row.pjgt.id_pjgt }}</span>
                    <span v-else-if="!row.pjgt" class="text-gray-400 italic">Belum ditentukan</span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end mt-4" v-if="wilayahs.links && wilayahs.links.length > 3">
                <component
                    v-for="link in wilayahs.links"
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
            :message="`Hapus data Wilayah '${confirmDelete?.nama}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
