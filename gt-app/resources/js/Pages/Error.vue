<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const goBack = () => window.history.back();
</script>

<template>
    <div class="min-h-screen bg-gray-900 flex items-center justify-center p-6">
        <div class="text-center max-w-md">
            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="h-24 w-24 rounded-3xl bg-red-500/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m0 0v2m0-2h2m-2 0H10m-2-5a9 9 0 1114 0 9 9 0 01-14 0z" />
                        <circle cx="12" cy="10" r="1.5" fill="currentColor" />
                    </svg>
                </div>
            </div>

            <!-- Error code -->
            <p class="text-8xl font-black text-white/10 leading-none select-none">403</p>

            <!-- Message -->
            <h1 class="text-2xl font-bold text-white mt-2">Access Denied</h1>
            <p class="text-gray-400 mt-3 text-sm leading-relaxed">
                You don't have permission to access this page.<br>
                Contact your administrator if you think this is a mistake.
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-8">
                <Link
                    :href="route('dashboard')"
                    class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors"
                >
                    Back to Dashboard
                </Link>
                <button
                    @click="goBack"
                    class="px-5 py-2.5 border border-gray-600 text-gray-300 text-sm font-semibold rounded-xl hover:bg-gray-800 transition-colors"
                >
                    Go Back
                </button>
            </div>
        </div>
    </div>
</template>
