<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps<{
    roles: { id: number; name: string }[];
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});

const submit = () => form.post(route('users.store'));
</script>

<template>
    <Head title="Create User" />
    <AuthenticatedLayout>
        <div class="max-w-2xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('users.index')" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">Create User</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input v-model="form.name" type="text" class="input" placeholder="Full name" required />
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="form.email" type="email" class="input" placeholder="email@example.com" required />
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input v-model="form.password" type="password" class="input" placeholder="Min 8 characters" required />
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input v-model="form.password_confirmation" type="password" class="input" placeholder="Repeat password" required />
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
                        {{ form.processing ? 'Saving…' : 'Create User' }}
                    </button>
                    <Link :href="route('users.index')" class="btn-secondary">Cancel</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
