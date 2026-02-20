<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Column {
    key: string;
    label: string;
    class?: string;
}

withDefaults(defineProps<{
    columns: Column[];
    rows: any[];
    editRoute?: string;
    deleteRoute?: string;
    createRoute?: string;
    createLabel?: string;
    emptyText?: string;
    canCreate?: boolean;
    canEdit?: boolean;
    canDelete?: boolean;
}>(), {
    canCreate: true,
    canEdit: true,
    canDelete: true,
});

defineEmits<{ (e: 'delete', row: any): void }>();
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-4" v-if="createRoute && canCreate">
            <slot name="filters" />
            <Link
                :href="route(createRoute)"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors ml-auto"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ createLabel ?? 'Create New' }}
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            :class="col.class"
                        >
                            {{ col.label }}
                        </th>
                        <th
                            v-if="canEdit || canDelete"
                            class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-if="rows.length === 0">
                        <td :colspan="(canEdit || canDelete) ? columns.length + 1 : columns.length" class="px-6 py-12 text-center text-gray-400 text-sm">
                            {{ emptyText ?? 'No records found.' }}
                        </td>
                    </tr>
                    <tr v-for="row in rows" :key="row.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td
                            v-for="col in columns"
                            :key="col.key"
                            class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300"
                            :class="col.class"
                        >
                            <slot :name="col.key" :row="row">{{ row[col.key] }}</slot>
                        </td>
                        <td v-if="canEdit || canDelete" class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <slot name="actions" :row="row">
                                    <Link
                                        v-if="editRoute && canEdit"
                                        :href="route(editRoute, row.id)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="deleteRoute && canDelete"
                                        @click="$emit('delete', row)"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium"
                                    >
                                        Delete
                                    </button>
                                </slot>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
