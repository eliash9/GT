<script setup lang="ts">
import { ref, computed, defineComponent, h } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { usePermission } from '@/composables/usePermission';

import { useDarkMode } from '@/composables/useDarkMode';

const page = usePage();
const user = computed(() => page.props.auth.user as any);
const { can, hasRole } = usePermission();
const { isDark, toggleDarkMode } = useDarkMode();

const sidebarOpen = ref(true);

interface NavItem {
    label: string;
    icon: string;
    route?: string;
    permission?: string;
    children?: { label: string; route: string; permission: string }[];
}

// Nav items - each has a required permission. null = always visible.
const navItems = computed((): NavItem[] => {
    const items: NavItem[] = [
        { label: 'Dashboard', icon: 'home', route: 'dashboard' },
    ];

    if (can('view users') || can('view roles')) {
        items.push({
            label: 'User Management', icon: 'users',
            children: [
                ...(can('view users')  ? [{ label: 'Users', route: 'users.index', permission: 'view users' }] : []),
                ...(can('view roles')  ? [{ label: 'Roles', route: 'roles.index', permission: 'view roles' }] : []),
            ],
        });
    }

    if (can('view products'))   items.push({ label: 'Products',   icon: 'package', route: 'products.index',   permission: 'view products' });
    if (can('view categories')) items.push({ label: 'Categories', icon: 'tag',     route: 'categories.index', permission: 'view categories' });
    if (can('view settings'))   items.push({ label: 'Settings',   icon: 'settings', route: 'settings.index',  permission: 'view settings' });

    return items;
});

const openGroups = ref<string[]>([]);
const toggleGroup = (label: string) => {
    const i = openGroups.value.indexOf(label);
    if (i >= 0) openGroups.value.splice(i, 1);
    else openGroups.value.push(label);
};
const isGroupOpen = (label: string) => openGroups.value.includes(label);

// Icons map
const icons: Record<string, string> = {
    home:     'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    users:    'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    package:  'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    tag:      'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z',
    settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
};
</script>

<template>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900 overflow-hidden">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="flex flex-col bg-gray-900 text-white transition-all duration-300 shrink-0"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-700">
                <Link :href="route('dashboard')" class="flex items-center gap-2 min-w-0">
                    <ApplicationLogo class="h-8 w-8 fill-current text-indigo-400 shrink-0" />
                    <span v-if="sidebarOpen" class="font-bold text-lg text-white truncate">GT App</span>
                </Link>
                <button @click="sidebarOpen = !sidebarOpen" class="ml-auto text-gray-400 hover:text-white shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 py-4 overflow-y-auto">
                <template v-for="item in navItems" :key="item.label">
                    <!-- Group with children -->
                    <div v-if="item.children">
                        <button
                            @click="toggleGroup(item.label)"
                            class="flex items-center w-full px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[item.icon]" />
                            </svg>
                            <span v-if="sidebarOpen" class="ml-3 flex-1 text-left">{{ item.label }}</span>
                            <svg v-if="sidebarOpen" xmlns="http://www.w3.org/2000/svg" :class="['h-4 w-4 transition-transform', isGroupOpen(item.label) ? 'rotate-180' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-if="isGroupOpen(item.label) && sidebarOpen" class="bg-gray-800">
                            <Link
                                v-for="child in item.children"
                                :key="child.route"
                                :href="route(child.route)"
                                :class="['flex items-center pl-12 pr-4 py-2 text-sm transition-colors',
                                    route().current(child.route.replace('.index', '.*'))
                                        ? 'text-indigo-400'
                                        : 'text-gray-400 hover:text-white hover:bg-gray-700']"
                            >
                                {{ child.label }}
                            </Link>
                        </div>
                    </div>

                    <!-- Single link -->
                    <Link
                        v-else
                        :href="route(item.route!)"
                        :class="['flex items-center px-4 py-2.5 text-sm transition-colors',
                            route().current(item.route!.replace('.index', '.*'))
                                ? 'bg-indigo-600 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white']"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[item.icon]" />
                        </svg>
                        <span v-if="sidebarOpen" class="ml-3">{{ item.label }}</span>
                    </Link>
                </template>
            </nav>

            <!-- User info + role badge -->
            <div class="border-t border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-bold shrink-0">
                        {{ user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div v-if="sidebarOpen" class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <div class="flex items-center gap-1 mt-0.5">
                            <span
                                v-for="role in page.props.auth.roles"
                                :key="role"
                                class="inline-block px-1.5 py-0.5 text-[10px] rounded font-medium"
                                :class="role === 'super-admin' ? 'bg-purple-500/30 text-purple-300' :
                                        role === 'admin'       ? 'bg-indigo-500/30 text-indigo-300' :
                                        'bg-gray-500/30 text-gray-400'"
                            >{{ role }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            <!-- Topbar -->
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center px-6 gap-4 shrink-0">
                <slot name="header" />
                <div class="flex-1" />

                <!-- Flash messages in topbar -->
                <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="page.props.flash?.success" class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ page.props.flash.success }}
                    </div>
                </Transition>
                <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="page.props.flash?.error" class="flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ page.props.flash.error }}
                    </div>
                </Transition>

                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-bold">
                                {{ user?.name?.charAt(0)?.toUpperCase() }}
                            </div>
                            <span class="hidden sm:block">{{ user?.name }}</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                    </template>
                </Dropdown>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
