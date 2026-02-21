<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';

const LeafletMap = defineAsyncComponent(() => import('@/Components/LeafletMap.vue'));

const props = defineProps<{
    lembaga: any;
    wilayahs: any[];
    pjgts: any[];
}>();

const form = useForm({
    nama: props.lembaga.nama,
    alamat: props.lembaga.alamat || '',
    latitude: props.lembaga.latitude || '',
    longitude: props.lembaga.longitude || '',
    urlmap: props.lembaga.urlmap || '',
    status: props.lembaga.status || 'aktif',
    wilayah_id: props.lembaga.wilayah_id || '',
    pjgt_id: props.lembaga.pjgt_id || '',
});

const submit = () => form.put(route('lembagas.update', props.lembaga.id));
</script>

<template>
    <Head title="Edit Lembaga" />
    <AuthenticatedLayout>
        <div class="max-w-4xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('lembagas.show', lembaga.id)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Data Lembaga</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Info Dasar -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Informasi Dasar</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lembaga <span class="text-red-500">*</span></label>
                            <input v-model="form.nama" type="text"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
                                placeholder="Nama Lembaga Penerima GT" required />
                            <p v-if="form.errors.nama" class="text-red-500 text-xs mt-1">{{ form.errors.nama }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Lengkap</label>
                            <textarea v-model="form.alamat"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
                                rows="3" placeholder="Alamat lengkap lembaga"></textarea>
                            <p v-if="form.errors.alamat" class="text-red-500 text-xs mt-1">{{ form.errors.alamat }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wilayah</label>
                            <select v-model="form.wilayah_id"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300">
                                <option value="">— Pilih Wilayah —</option>
                                <option v-for="w in wilayahs" :key="w.id" :value="w.id">{{ w.nama }}</option>
                            </select>
                            <p v-if="form.errors.wilayah_id" class="text-red-500 text-xs mt-1">{{ form.errors.wilayah_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Koordinator PJGT</label>
                            <select v-model="form.pjgt_id"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300">
                                <option value="">— Pilih PJGT —</option>
                                <option v-for="p in pjgts" :key="p.id" :value="p.id">{{ p.nama }}</option>
                            </select>
                            <p v-if="form.errors.pjgt_id" class="text-red-500 text-xs mt-1">{{ form.errors.pjgt_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                            <select v-model="form.status"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300">
                                <option value="aktif">Aktif</option>
                                <option value="non-aktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Lokasi & Peta -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-1">📍 Lokasi di Peta</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Klik pada peta untuk memindahkan pin ke lokasi lembaga, atau seret pin yang sudah ada.</p>

                    <!-- Map Picker -->
                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-4">
                        <Suspense>
                            <LeafletMap
                                mode="picker"
                                :lat="form.latitude"
                                :lng="form.longitude"
                                height="380px"
                                @update:lat="form.latitude = $event"
                                @update:lng="form.longitude = $event"
                            />
                        </Suspense>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                            <input v-model="form.latitude" type="text"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 font-mono"
                                placeholder="-7.0000000" />
                            <p v-if="form.errors.latitude" class="text-red-500 text-xs mt-1">{{ form.errors.latitude }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                            <input v-model="form.longitude" type="text"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 font-mono"
                                placeholder="113.0000000" />
                            <p v-if="form.errors.longitude" class="text-red-500 text-xs mt-1">{{ form.errors.longitude }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL Google Maps</label>
                            <input v-model="form.urlmap" type="text"
                                class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
                                placeholder="https://maps.google.com/..." />
                            <p v-if="form.errors.urlmap" class="text-red-500 text-xs mt-1">{{ form.errors.urlmap }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Perubahan' }}
                    </button>
                    <Link :href="route('lembagas.show', lembaga.id)"
                        class="inline-flex items-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
