<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps<{
    role: any;
    permissions: { id: number; name: string }[];
}>();

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map((p: any) => p.id) as number[],
});

const submit = () => form.put(route('roles.update', props.role.id));
const togglePerm = (id: number) => {
    const i = form.permissions.indexOf(id);
    if (i >= 0) form.permissions.splice(i, 1);
    else form.permissions.push(id);
};

const grouped = props.permissions.reduce((acc: Record<string, any[]>, p) => {
    const group = p.name.split(' ').slice(1).join(' ') || 'other';
    if (!acc[group]) acc[group] = [];
    acc[group].push(p);
    return acc;
}, {});

const getPermId = (perms: any[], action: string) => {
    const p = perms.find(x => x.name.startsWith(action + ' '));
    return p ? p.id : null;
};
</script>

<template>
    <Head title="Edit Role" />
    <AuthenticatedLayout>
        <div class="max-w-4xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('roles.index')" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Role: {{ role.name }}</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 overflow-x-auto">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Permissions Matrix</h3>
                    
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 px-3 text-gray-600 dark:text-gray-400 font-semibold">Module</th>
                                <th class="py-2 px-3 text-center text-gray-600 dark:text-gray-400 font-semibold">View</th>
                                <th class="py-2 px-3 text-center text-gray-600 dark:text-gray-400 font-semibold">Create</th>
                                <th class="py-2 px-3 text-center text-gray-600 dark:text-gray-400 font-semibold">Edit</th>
                                <th class="py-2 px-3 text-center text-gray-600 dark:text-gray-400 font-semibold">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(perms, group) in grouped" :key="group" class="border-b dark:border-gray-700 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-3 px-3 font-medium text-gray-800 dark:text-gray-200 capitalize">{{ group }}</td>
                                <td v-for="action in ['view', 'create', 'edit', 'delete']" :key="action" class="py-3 px-3 text-center">
                                    <template v-if="getPermId(perms, action)">
                                        <input 
                                            type="checkbox" 
                                            :value="getPermId(perms, action)" 
                                            :checked="form.permissions.includes(getPermId(perms, action)!)" 
                                            @change="togglePerm(getPermId(perms, action)!)" 
                                            class="rounded w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm cursor-pointer" 
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors">
                        {{ form.processing ? 'Saving…' : 'Update Role' }}
                    </button>
                    <Link :href="route('roles.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
