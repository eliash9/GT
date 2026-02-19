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
</script>

<template>
    <Head title="Edit Role" />
    <AuthenticatedLayout>
        <div class="max-w-3xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('roles.index')" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">Edit Role: {{ role.name }}</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                    <input v-model="form.name" type="text" class="input" required />
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Permissions</h3>
                    <div class="space-y-4">
                        <div v-for="(perms, group) in grouped" :key="group">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 capitalize">{{ group }}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <label v-for="perm in perms" :key="perm.id" class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 border border-gray-100">
                                    <input type="checkbox" :value="perm.id" :checked="form.permissions.includes(perm.id)" @change="togglePerm(perm.id)" class="rounded text-indigo-600" />
                                    <span class="text-sm text-gray-700">{{ perm.name.split(' ')[0] }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" :disabled="form.processing" class="btn-primary">
                        {{ form.processing ? 'Saving…' : 'Update Role' }}
                    </button>
                    <Link :href="route('roles.index')" class="btn-secondary">Cancel</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
