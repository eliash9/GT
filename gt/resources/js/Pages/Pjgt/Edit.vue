<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps<{
    pjgt: any;
}>();

const form = useForm({
    id_pjgt: props.pjgt.id_pjgt || '',
    nama: props.pjgt.nama,
    no_hp: props.pjgt.no_hp || '',
});

const submit = () => form.put(route('pjgts.update', props.pjgt.id));
</script>

<template>
    <Head title="Edit PJGT" />
    <AuthenticatedLayout>
        <div class="max-w-xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('pjgts.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Data PJGT</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID PJGT</label>
                        <input v-model="form.id_pjgt" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="ID PJGT (Biarkan kosong untuk otomatis atau angka bebas)" />
                        <p v-if="form.errors.id_pjgt" class="text-red-500 text-xs mt-1">{{ form.errors.id_pjgt }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                        <input v-model="form.nama" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="Nama Lengkap PJGT" required />
                        <p v-if="form.errors.nama" class="text-red-500 text-xs mt-1">{{ form.errors.nama }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor HP</label>
                        <input v-model="form.no_hp" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="08..." />
                        <p v-if="form.errors.no_hp" class="text-red-500 text-xs mt-1">{{ form.errors.no_hp }}</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Perubahan' }}
                    </button>
                    <Link :href="route('pjgts.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
