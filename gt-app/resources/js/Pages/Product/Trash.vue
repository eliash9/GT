<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePermission } from '@/composables/usePermission';

const props = defineProps<{
    products: any;
}>();

const { can } = usePermission();

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'price', label: 'Price' },
    { key: 'is_active', label: 'Status' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('products.force-delete', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};

const handleRestore = (id: number) => {
    router.post(route('products.restore', id));
};
</script>

<template>
    <Head title="Products Trash" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <Link :href="route('products.index')" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Products Trash</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Restore or permanently delete products.</p>
                </div>
            </div>

            <DataTable
                :columns="columns"
                :rows="products.data"
                :can-create="false"
                :can-edit="false"
                :can-delete="can('delete products')"
                @delete="confirmDelete = $event"
            >
                <template #price="{ row }">
                    ${{ Number(row.price).toLocaleString() }}
                </template>
                <template #is_active="{ row }">
                    <span :class="['px-2.5 py-1 text-xs font-semibold rounded-full', row.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400']">
                        {{ row.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>
                <template #actions="{ row }">
                    <button @click="handleRestore(row.id)" class="px-2 py-1 text-sm text-emerald-600 hover:bg-emerald-50 rounded-lg dark:text-emerald-400 dark:hover:bg-emerald-900/30 transition-colors">Restore</button>
                    <button @click="confirmDelete = row" class="px-2 py-1 text-sm text-red-600 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-red-900/30 transition-colors">Delete Permanently</button>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="flex gap-1 mt-4" v-if="products.links?.length > 3">
                <component
                    v-for="link in products.links"
                    :key="link.label"
                    :is="link.url ? 'Link' : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border dark:border-gray-700"
                    :class="link.active 
                        ? 'bg-indigo-600 text-white border-indigo-600' 
                        : link.url 
                            ? 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 border-gray-200' 
                            : 'text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-900 border-gray-200 cursor-not-allowed'"
                />
            </div>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            message="Are you sure you want to permanently delete this product? This action cannot be undone."
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
