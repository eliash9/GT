<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps<{
    santri: any;
}>();

const form = useForm({
    nis: props.santri.nis,
    nama: props.santri.nama,
    jenis_kelamin: props.santri.jenis_kelamin,
    tempat_lahir: props.santri.tempat_lahir,
    tanggal_lahir: props.santri.tanggal_lahir,
    alamat_lengkap: props.santri.alamat_lengkap ?? '',
    no_hp: props.santri.no_hp ?? '',
    nama_ayah: props.santri.nama_ayah ?? '',
    angkatan: props.santri.angkatan,
    kelas: props.santri.kelas ?? '',
    status_tugas: props.santri.status_tugas,
    foto: null as File | null,
    _method: 'PUT',
});

const submit = () => form.post(route('santris.update', props.santri.id), {
    forceFormData: true,
});
</script>

<template>
    <Head title="Edit Santri" />
    <AuthenticatedLayout>
        <div class="max-w-2xl">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('santris.show', santri.id)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Data Santri</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIS</label>
                        <input v-model="form.nis" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="Nomor Induk Santri" required />
                        <p v-if="form.errors.nis" class="text-red-500 text-xs mt-1">{{ form.errors.nis }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                        <input v-model="form.nama" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="Nama Lengkap" required />
                        <p v-if="form.errors.nama" class="text-red-500 text-xs mt-1">{{ form.errors.nama }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin</label>
                        <select v-model="form.jenis_kelamin" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" required>
                            <option value="">— Pilih Kelamin —</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <p v-if="form.errors.jenis_kelamin" class="text-red-500 text-xs mt-1">{{ form.errors.jenis_kelamin }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tempat Lahir</label>
                        <input v-model="form.tempat_lahir" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="Kota Kelahiran" required />
                        <p v-if="form.errors.tempat_lahir" class="text-red-500 text-xs mt-1">{{ form.errors.tempat_lahir }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir</label>
                        <input v-model="form.tanggal_lahir" type="date" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" required />
                        <p v-if="form.errors.tanggal_lahir" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_lahir }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor HP</label>
                        <input v-model="form.no_hp" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="08..." />
                        <p v-if="form.errors.no_hp" class="text-red-500 text-xs mt-1">{{ form.errors.no_hp }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Lengkap</label>
                        <textarea v-model="form.alamat_lengkap" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" rows="3" placeholder="Alamat lengkap"></textarea>
                        <p v-if="form.errors.alamat_lengkap" class="text-red-500 text-xs mt-1">{{ form.errors.alamat_lengkap }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Ayah / Wali</label>
                        <input v-model="form.nama_ayah" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="Nama Ayah/Wali" />
                        <p v-if="form.errors.nama_ayah" class="text-red-500 text-xs mt-1">{{ form.errors.nama_ayah }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                        <input v-model="form.kelas" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="Kelas saat ini" />
                        <p v-if="form.errors.kelas" class="text-red-500 text-xs mt-1">{{ form.errors.kelas }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Foto (Opsional: Kosongkan jika tidak ingin mengubah)</label>
                        <div class="flex items-center gap-4">
                            <img v-if="props.santri.foto" :src="'/storage/' + props.santri.foto" class="h-16 w-16 rounded-full object-cover border border-gray-200" alt="Foto saat ini" />
                            <div class="flex-1">
                                <input @input="form.foto = ($event.target as HTMLInputElement).files?.[0] ?? null" type="file" accept="image/*" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" />
                                <p v-if="form.errors.foto" class="text-red-500 text-xs mt-1">{{ form.errors.foto }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Angkatan (Tahun Lulus)</label>
                        <input v-model="form.angkatan" type="number" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" placeholder="2026" required />
                        <p v-if="form.errors.angkatan" class="text-red-500 text-xs mt-1">{{ form.errors.angkatan }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Penugasan</label>
                        <select v-model="form.status_tugas" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" required>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Sedang Bertugas">Sedang Bertugas</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        <p v-if="form.errors.status_tugas" class="text-red-500 text-xs mt-1">{{ form.errors.status_tugas }}</p>
                    </div>

                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Perubahan' }}
                    </button>
                    <Link :href="route('santris.show', santri.id)" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
