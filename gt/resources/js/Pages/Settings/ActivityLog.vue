<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    activities: { data: any[]; links: any[] };
}>();

const columns = [
    { key: 'created_at', label: 'Date' },
    { key: 'causer_name', label: 'User' },
    { key: 'description', label: 'Action' },
    { key: 'subject_type', label: 'Type' },
];
</script>

<template>
    <Head title="Activity Log" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Activity Log</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Audit trail for user actions and system events.</p>
            </div>

            <DataTable
                :columns="columns"
                :rows="activities.data"
                :can-create="false"
                :can-edit="false"
                :can-delete="false"
            >
                <template #created_at="{ row }">
                    {{ new Date(row.created_at).toLocaleString() }}
                </template>
                <template #causer_name="{ row }">
                    {{ row.causer?.name ?? 'System' }}
                </template>
                <template #subject_type="{ row }">
                    <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded-full font-mono text-gray-600 dark:text-gray-300">
                        {{ row.subject_type ? row.subject_type.split('\\').pop() : 'N/A' }}
                    </span>
                </template>
                <template #description="{ row }">
                    <span class="font-medium capitalize">{{ row.description }}</span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end" v-if="activities.links?.length > 3">
                <component
                    v-for="link in activities.links"
                    :key="link.label"
                    :is="link.url ? 'a' : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border"
                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : link.url ? 'border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 dark:text-gray-300' : 'border-gray-200 text-gray-400 cursor-not-allowed dark:border-gray-700 dark:text-gray-600'"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
