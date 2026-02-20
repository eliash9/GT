<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePermission } from '@/composables/usePermission';

const props = defineProps<{
    products: { data: any[]; links: any[] };
}>();

const { can } = usePermission();

const columns = [
    { key: 'name',      label: 'Name' },
    { key: 'price',     label: 'Price' },
    { key: 'is_active', label: 'Status' },
];

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('products.destroy', confirmDelete.value.id), {
        onSuccess: () => confirmDelete.value = null,
    });
};

const formatPrice = (value: number | string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(Number(value));
};

const handleImport = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        router.post(route('products.import'), { file: target.files[0] }, {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Head title="Products" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Products</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your product catalog.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="products.data"
                create-route="products.create"
                create-label="Add Product"
                edit-route="products.edit"
                delete-route="products.destroy"
                :can-create="can('create products')"
                :can-edit="can('edit products')"
                :can-delete="can('delete products')"
                @delete="confirmDelete = $event"
            >
                <template #filters>
                    <div class="flex flex-wrap items-center gap-2">
                        <Link :href="route('products.trash')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Trash
                        </Link>
                        <a :href="route('products.export')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Export
                        </a>
                        <label class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/></svg>
                            Import
                            <input type="file" class="hidden" @change="handleImport" accept=".xlsx,.xls,.csv" />
                        </label>
                    </div>
                </template>
                <template #price="{ row }">
                    {{ formatPrice(row.price) }}
                </template>
                <template #is_active="{ row }">
                    <span
                        :class="row.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                        class="inline-block px-2 py-0.5 text-xs font-medium rounded-full"
                    >
                        {{ row.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end" v-if="products.links && products.links.length > 3">
                <component
                    v-for="link in products.links"
                    :key="link.label"
                    :is="link.url ? 'a' : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border"
                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : link.url ? 'border-gray-300 hover:bg-gray-50' : 'border-gray-200 text-gray-400 cursor-not-allowed'"
                />
            </div>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :message="`Delete product '${confirmDelete?.name}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
