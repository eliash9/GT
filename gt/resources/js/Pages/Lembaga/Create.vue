<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps<{
    wilayahs: any[];
    pjgts: any[];
}>();

const form = useForm({
    nama: '',
    alamat: '',
    latitude: '',
    longitude: '',
    urlmap: '',
    status: 'aktif',
    wilayah_id: '',
    pjgt_id: '',
});

const submit = () => form.post(route('lembagas.store'));
</script>

<template>
    <Head title="Tambah Lembaga" />
    <AuthenticatedLayout>
        <div class="max-w-2xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('lembagas.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tambah Data Lembaga</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lembaga</label>
                        <input v-model="form.nama" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="Nama Lembaga Penerima GT" required />
                        <p v-if="form.errors.nama" class="text-red-500 text-xs mt-1">{{ form.errors.nama }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Lengkap</label>
                        <textarea v-model="form.alamat" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" rows="3" placeholder="Alamat lengkap"></textarea>
                        <p v-if="form.errors.alamat" class="text-red-500 text-xs mt-1">{{ form.errors.alamat }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wilayah</label>
                        <select v-model="form.wilayah_id" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300">
                            <option value="">— Tidak ada —</option>
                            <option v-for="wilayah in wilayahs" :key="wilayah.id" :value="wilayah.id">{{ wilayah.nama }}</option>
                        </select>
                        <p v-if="form.errors.wilayah_id" class="text-red-500 text-xs mt-1">{{ form.errors.wilayah_id }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PJGT Lembaga</label>
                        <select v-model="form.pjgt_id" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300">
                            <option value="">— Tidak ada —</option>
                            <option v-for="pjgt in pjgts" :key="pjgt.id" :value="pjgt.id">{{ pjgt.nama }}</option>
                        </select>
                        <p v-if="form.errors.pjgt_id" class="text-red-500 text-xs mt-1">{{ form.errors.pjgt_id }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Data Lokasi (Peta)</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                                    <input v-model="form.latitude" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="-7.XXX" />
                                    <p v-if="form.errors.latitude" class="text-red-500 text-xs mt-1">{{ form.errors.latitude }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                                    <input v-model="form.longitude" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="113.XXX" />
                                    <p v-if="form.errors.longitude" class="text-red-500 text-xs mt-1">{{ form.errors.longitude }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL Google Maps</label>
                                    <input v-model="form.urlmap" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="https://maps.google.com/..." />
                                    <p v-if="form.errors.urlmap" class="text-red-500 text-xs mt-1">{{ form.errors.urlmap }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Data' }}
                    </button>
                    <Link :href="route('lembagas.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
