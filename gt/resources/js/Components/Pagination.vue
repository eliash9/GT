<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}>();
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap gap-1 justify-end">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    class="px-3 py-1.5 text-sm rounded-lg border dark:border-gray-700 transition-colors"
                    :class="link.active 
                        ? 'bg-indigo-600 text-white border-indigo-600' 
                        : 'border-gray-300 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                    v-html="link.label"
                    preserve-state
                />
            </template>
        </div>
    </div>
</template>
