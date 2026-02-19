<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    stats: { users: number; products: number; categories: number };
}>();

const cards = [
    { label: 'Total Users',      value: props.stats.users,      icon: 'users',   color: 'bg-indigo-500',  href: 'users.index' },
    { label: 'Total Products',   value: props.stats.products,   icon: 'package', color: 'bg-emerald-500', href: 'products.index' },
    { label: 'Total Categories', value: props.stats.categories, icon: 'tag',     color: 'bg-amber-500',   href: 'categories.index' },
];

const iconPaths: Record<string, string> = {
    users:   'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    package: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    tag:     'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z',
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Page title -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Selamat datang! Berikut ringkasan sistem Anda.</p>
            </div>

            <!-- Stat cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <Link
                    v-for="card in cards"
                    :key="card.label"
                    :href="route(card.href)"
                    class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow group"
                >
                    <div :class="[card.color, 'h-14 w-14 rounded-2xl flex items-center justify-center shrink-0']">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPaths[card.icon]" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ card.label }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ card.value }}</p>
                    </div>
                </Link>
            </div>

            <!-- Quick actions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <Link :href="route('users.create')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition-colors text-center group">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 group-hover:bg-indigo-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-700">New User</span>
                    </Link>
                    <Link :href="route('products.create')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition-colors text-center group">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-emerald-700">New Product</span>
                    </Link>
                    <Link :href="route('categories.create')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-amber-400 hover:bg-amber-50 transition-colors text-center group">
                        <div class="h-10 w-10 rounded-full bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-amber-700">New Category</span>
                    </Link>
                    <Link :href="route('settings.index')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-colors text-center group">
                        <div class="h-10 w-10 rounded-full bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-gray-700">Settings</span>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
