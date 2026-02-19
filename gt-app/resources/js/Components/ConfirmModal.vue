<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    show: boolean;
    title?: string;
    message?: string;
}>();

const emit = defineEmits<{
    (e: 'confirm'): void;
    (e: 'cancel'): void;
}>();
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="emit('cancel')" />
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ title ?? 'Confirm Delete' }}</h3>
                        <p class="text-sm text-gray-500">{{ message ?? 'This action cannot be undone.' }}</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button @click="emit('cancel')" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                    <button @click="emit('confirm')" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Delete</button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
