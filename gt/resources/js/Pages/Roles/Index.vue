<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePermission } from '@/composables/usePermission';

const props = defineProps<{
    roles: any[];
}>();

const { can } = usePermission();

const columns = [
    { key: 'name',        label: 'Role Name' },
    { key: 'permissions', label: 'Permissions' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('roles.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};
</script>

<template>
    <Head title="Roles" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Role Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage roles and their permissions.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="roles"
                create-route="roles.create"
                create-label="Add Role"
                edit-route="roles.edit"
                delete-route="roles.destroy"
                :can-create="can('create roles')"
                :can-edit="can('edit roles')"
                :can-delete="can('delete roles')"
                @delete="confirmDelete = $event"
            >
                <template #permissions="{ row }">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="perm in row.permissions.slice(0, 5)"
                            :key="perm.id"
                            class="inline-block px-2 py-0.5 text-xs rounded-full bg-blue-50 text-blue-700 font-medium"
                        >{{ perm.name }}</span>
                        <span v-if="row.permissions.length > 5" class="text-xs text-gray-400 italic">+{{ row.permissions.length - 5 }} more</span>
                    </div>
                </template>
            </DataTable>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :message="`Delete role '${confirmDelete?.name}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
