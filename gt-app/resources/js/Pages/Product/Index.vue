<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, router } from '@inertiajs/vue3';
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
