<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    show: boolean;
    title?: string;
    message?: string;
    confirmText?: string;
    cancelText?: string;
    type?: 'danger' | 'info';
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
                    <div class="h-12 w-12 rounded-full flex items-center justify-center" :class="type === 'info' ? 'bg-indigo-100' : 'bg-red-100'">
                        <svg v-if="type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ title ?? 'Confirm Delete' }}</h3>
                        <p class="text-sm text-gray-500">{{ message ?? 'This action cannot be undone.' }}</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button @click="emit('cancel')" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">{{ cancelText ?? 'Cancel' }}</button>
                    <button @click="emit('confirm')" 
                        class="px-4 py-2 text-sm text-white rounded-lg transition-colors"
                        :class="type === 'info' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-red-600 hover:bg-red-700'"
                    >{{ confirmText ?? 'Delete' }}</button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
