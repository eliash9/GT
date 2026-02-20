<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePermission } from '@/composables/usePermission';

const props = defineProps<{
    categories: { data: any[]; links: any[] };
}>();

const { can } = usePermission();

const columns = [
    { key: 'name',        label: 'Name' },
    { key: 'description', label: 'Description' },
    { key: 'is_active',   label: 'Status' },
    { key: 'created_at',  label: 'Created' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('categories.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};
</script>

<template>
    <Head title="Categories" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Categories</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage content categories.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="categories.data"
                create-route="categories.create"
                create-label="Add Category"
                edit-route="categories.edit"
                delete-route="categories.destroy"
                :can-create="can('create categories')"
                :can-edit="can('edit categories')"
                :can-delete="can('delete categories')"
                @delete="confirmDelete = $event"
            >
                <template #is_active="{ row }">
                    <span
                        :class="row.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                        class="inline-block px-2 py-0.5 text-xs font-medium rounded-full"
                    >
                        {{ row.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>
                <template #created_at="{ row }">
                    {{ new Date(row.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                </template>
            </DataTable>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :message="`Delete category '${confirmDelete?.name}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
