<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

const form = useForm({
    title: '',
    description: '',
    type: 'event',
    allow_gt: true,
    allow_pjgt: true,
    start_at: '',
    end_at: '',
});

const submit = () => {
    form.post(route('attendance-sessions.store'));
};
</script>

<template>
    <Head title="Buat Sesi Absensi" />
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('attendance-sessions.index')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Sesi Absensi Baru</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 space-y-6">
                <div>
                    <InputLabel for="title" value="Nama Kegiatan / Sesi" />
                    <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" required autofocus placeholder="Contoh: Rapat Koordinasi Wilayah" />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="description" value="Deskripsi / Catatan" />
                    <textarea id="description" v-model="form.description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" rows="3"></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="type" value="Tipe Kegiatan" />
                        <select id="type" v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                            <option value="departure">Keberangkatan</option>
                            <option value="meeting">Rapat</option>
                            <option value="event">Kegiatan Umum</option>
                            <option value="other">Lainnya</option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>

                    <div class="flex flex-col justify-center space-y-3">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.allow_gt" name="allow_gt" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">GT Boleh Absen</span>
                        </label>
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.allow_pjgt" name="allow_pjgt" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">PJGT/Korwil Boleh Absen</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50 dark:border-gray-700">
                    <div>
                        <InputLabel for="start_at" value="Waktu Mulai (Opsional)" />
                        <TextInput id="start_at" v-model="form.start_at" type="datetime-local" class="mt-1 block w-full" />
                        <InputError :message="form.errors.start_at" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="end_at" value="Waktu Berakhir (Opsional)" />
                        <TextInput id="end_at" v-model="form.end_at" type="datetime-local" class="mt-1 block w-full" />
                        <InputError :message="form.errors.end_at" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end pt-6">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200" :disabled="form.processing">
                        Simpan dan Buka Sesi
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
