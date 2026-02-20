<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();

const appSettings = usePage().props.app_settings as any;
const appName = appSettings?.name || 'Sistem Informasi Guru Tugas';
</script>

<template>
    <Head title="Selamat Datang" />
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col font-sans">
        <!-- Navigation -->
        <nav class="w-full px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 flex items-center justify-center shrink-0">
                    <ApplicationLogo />
                </div>
                <span class="font-bold text-xl tracking-tight hidden sm:block text-indigo-900 dark:text-indigo-400">
                    {{ appName }}
                </span>
            </div>
            <div v-if="canLogin" class="flex items-center gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-sm font-semibold text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400 transition"
                >
                    Menuju Dashboard &rarr;
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-sm font-semibold text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400 transition"
                    >
                        Log in
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm"
                    >
                        Daftar
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="flex-1 flex flex-col items-center justify-center text-center px-6 py-12 lg:py-24">
            <div class="max-w-3xl space-y-8">
                <div class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium text-indigo-600 bg-indigo-50 dark:text-indigo-400 dark:bg-indigo-900/30 ring-1 ring-inset ring-indigo-600/20">
                    Versi Terbaru
                </div>
                
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-6xl text-balance">
                    Sistem Manajemen <span class="text-indigo-600 dark:text-indigo-400">Guru Tugas</span>
                </h1>
                
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400 text-balance max-w-2xl mx-auto">
                    Platform terpadu untuk mengelola penempatan santri, pendataan lembaga penerima, serta kemudahan komunikasi dengan Penanggung Jawab Guru Tugas (PJGT) wilayah.
                </p>

                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition"
                    >
                        Buka Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition"
                    >
                        Mulai Sekarang &rarr;
                    </Link>
                </div>
            </div>

            <!-- Features Quick View -->
            <div class="mt-24 grid grid-cols-1 gap-8 sm:grid-cols-3 max-w-5xl mx-auto text-left">
                <div class="relative bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="h-10 w-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Data Santri GT</h3>
                    <p class="mt-2 relative z-10 text-sm text-gray-600 dark:text-gray-400">Database lengkap santri yang sedang melaksanakan tugas pengabdian.</p>
                </div>
                
                <div class="relative bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="h-10 w-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pemetaan Lembaga</h3>
                    <p class="mt-2 relative z-10 text-sm text-gray-600 dark:text-gray-400">Kelola dan lihat penyebaran lembaga tujuan tempat santri mengabdi.</p>
                </div>

                <div class="relative bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="h-10 w-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Koordinasi PJGT</h3>
                    <p class="mt-2 relative z-10 text-sm text-gray-600 dark:text-gray-400">Integrasi langsung ke Koordinator wilayah secara mudah dan terpantau.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-8 border-t border-gray-200 dark:border-gray-800 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved. <span class="hidden sm:inline">&middot; V-{{ laravelVersion }}</span>
            </p>
        </footer>
    </div>
</template>
