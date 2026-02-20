<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePermission } from '@/composables/usePermission';

const props = defineProps<{
    users: { data: any[]; links: any[] };
}>();

const { can } = usePermission();

const columns = [
    { key: 'name',       label: 'Name' },
    { key: 'email',      label: 'Email' },
    { key: 'roles',      label: 'Role' },
    { key: 'created_at', label: 'Joined' },
];

const confirmDelete = ref<any>(null);

const handleDelete = (row: any) => confirmDelete.value = row;
const doDelete = () => {
    router.delete(route('users.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};
</script>

<template>
    <Head title="Users" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">User Management</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage user accounts and their roles.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="users.data"
                create-route="users.create"
                create-label="Add User"
                edit-route="users.edit"
                delete-route="users.destroy"
                :can-create="can('create users')"
                :can-edit="can('edit users')"
                :can-delete="can('delete users')"
                @delete="handleDelete"
            >
                <template #roles="{ row }">
                    <span
                        v-for="role in row.roles"
                        :key="role.id"
                        class="inline-block px-2 py-0.5 text-xs rounded-full font-medium mr-1"
                        :class="role.name === 'super-admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' :
                                role.name === 'admin'       ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' :
                                                              'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                    >{{ role.name }}</span>
                </template>
                <template #created_at="{ row }">
                    {{ new Date(row.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end" v-if="users.links?.length > 3">
                <component
                    v-for="link in users.links"
                    :key="link.label"
                    :is="link.url ? 'a' : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border"
                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : link.url ? 'border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 dark:text-gray-300' : 'border-gray-200 text-gray-400 cursor-not-allowed dark:border-gray-700 dark:text-gray-600'"
                />
            </div>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :message="`Delete user '${confirmDelete?.name}'? This cannot be undone.`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
