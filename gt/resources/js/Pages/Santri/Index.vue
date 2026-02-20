<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    santris: any[];
}>();

const columns = [
    { key: 'nis',           label: 'NIS' },
    { key: 'nama',          label: 'Nama Lengkap' },
    { key: 'jenis_kelamin', label: 'Gender' },
    { key: 'angkatan',      label: 'Angkatan' },
    { key: 'status_tugas',  label: 'Status Tugas' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('santris.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
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
                :rows="santris"
                create-route="santris.create"
                create-label="Tambah Santri"
                edit-route="santris.edit"
                delete-route="santris.destroy"
                :can-create="true"
                :can-edit="true"
                :can-delete="true"
                @delete="confirmDelete = $event"
            >
                <template #status_tugas="{ row }">
                    <span
                        class="inline-block px-2 py-0.5 text-xs rounded-full font-medium"
                        :class="row.status_tugas === 'Menunggu' ? 'bg-yellow-50 text-yellow-700' :
                                row.status_tugas === 'Sedang Bertugas' ? 'bg-blue-50 text-blue-700' :
                                'bg-green-50 text-green-700'"
                    >{{ row.status_tugas }}</span>
                </template>
            </DataTable>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :message="`Hapus data santri '${confirmDelete?.nama}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
