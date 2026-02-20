<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps<{
    user: any;
    roles: { id: number; name: string }[];
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.roles[0]?.name ?? '',
});

const submit = () => form.put(route('users.update', props.user.id));
</script>

<template>
    <Head title="Edit User" />
    <AuthenticatedLayout>
        <div class="max-w-2xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('users.index')" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input v-model="form.name" type="text" class="input" required />
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="form.email" type="email" class="input" required />
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input v-model="form.password" type="password" class="input" placeholder="Leave blank to keep current" />
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input v-model="form.password_confirmation" type="password" class="input" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select v-model="form.role" class="input" required>
                            <option value="">— Select Role —</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                        </select>
                        <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="btn-primary">
                        {{ form.processing ? 'Saving…' : 'Update User' }}
                    </button>
                    <Link :href="route('users.index')" class="btn-secondary">Cancel</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
