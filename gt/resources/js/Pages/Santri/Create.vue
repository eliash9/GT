<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    nis: '',
    nama: '',
    jenis_kelamin: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    alamat_lengkap: '',
    no_hp: '',
    nama_ayah: '',
    angkatan: '',
    kelas: '',
    kamar: '',
    status_tugas: 'Menunggu',
    foto: null as File | null,
    haliyah_jabatan: '',
    haliyah_keaktifan: '',
    haliyah_pelanggaran: '',
    akademisi: '',
    kelas_formal: '',
    nilai_ujian_tulis: '',
    nilai_ujian_materi: '',
    nilai_ujian_praktek_kelas: '',
    marhalah_alquran: '',
    status_seleksi: 'Belum Diproses',
    status_kelulusan: 'Belum Lulus',
});

const submit = () => form.post(route('santris.store'));
</script>

<template>
    <Head title="Tambah Santri" />
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('santris.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tambah Data Santri</h1>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kamar</label>
                        <input v-model="form.kamar" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300" placeholder="Contoh: A-1, B-2" />
                        <p v-if="form.errors.kamar" class="text-red-500 text-xs mt-1">{{ form.errors.kamar }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Foto</label>
                        <input @input="form.foto = ($event.target as HTMLInputElement).files?.[0] ?? null" type="file" accept="image/*" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300" />
                        <p v-if="form.errors.foto" class="text-red-500 text-xs mt-1">{{ form.errors.foto }}</p>
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

                <!-- Bagian Penilaian / Seleksi -->
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Data Evaluasi & Seleksi</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas Formal</label>
                            <input v-model="form.kelas_formal" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300" placeholder="Misal: Lulus SMA, Kelas XI" />
                            <p v-if="form.errors.kelas_formal" class="text-red-500 text-xs mt-1">{{ form.errors.kelas_formal }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Akademisi</label>
                            <select v-model="form.akademisi" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300">
                                <option value="">— Pilih Nilai —</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                            <p v-if="form.errors.akademisi" class="text-red-500 text-xs mt-1">{{ form.errors.akademisi }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Haliyah: Jabatan di Pondok</label>
                            <input v-model="form.haliyah_jabatan" type="text" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300" placeholder="Mis. Ketua Kamar" />
                            <p v-if="form.errors.haliyah_jabatan" class="text-red-500 text-xs mt-1">{{ form.errors.haliyah_jabatan }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Haliyah: Keaktifan</label>
                            <select v-model="form.haliyah_keaktifan" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300">
                                <option value="">— Pilih Level —</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                            <p v-if="form.errors.haliyah_keaktifan" class="text-red-500 text-xs mt-1">{{ form.errors.haliyah_keaktifan }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Haliyah: Pelanggaran (opsional)</label>
                            <textarea v-model="form.haliyah_pelanggaran" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300" rows="2" placeholder="Catatan pelanggaran jika ada"></textarea>
                            <p v-if="form.errors.haliyah_pelanggaran" class="text-red-500 text-xs mt-1">{{ form.errors.haliyah_pelanggaran }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkat Baca Al-Quran (Marhalah)</label>
                            <select v-model="form.marhalah_alquran" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300">
                                <option value="">— Pilih Tingkat —</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                            <p v-if="form.errors.marhalah_alquran" class="text-red-500 text-xs mt-1">{{ form.errors.marhalah_alquran }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Seleksi Awal</label>
                            <select v-model="form.status_seleksi" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300">
                                <option value="Belum Diproses">Belum Diproses</option>
                                <option value="Lolos Tahap Awal">Lolos Tahap Awal</option>
                                <option value="Lolos Tahap Akhir" :disabled="form.status_kelulusan !== 'Lulus'">Lolos Tahap Akhir</option>
                                <option value="Tidak Lolos">Tidak Lolos</option>
                            </select>
                            <p v-if="form.errors.status_seleksi" class="text-red-500 text-xs mt-1">{{ form.errors.status_seleksi }}</p>
                            <p v-if="form.status_kelulusan !== 'Lulus' && form.status_seleksi === 'Lolos Tahap Akhir'" class="text-red-500 text-xs mt-1">Harus Lulus untuk Lolos Tahap Akhir!</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Kelulusan</label>
                            <select v-model="form.status_kelulusan" @change="(form.status_kelulusan !== 'Lulus' && form.status_seleksi === 'Lolos Tahap Akhir') ? form.status_seleksi = 'Belum Diproses' : null" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300">
                                <option value="Belum Lulus">Belum Lulus</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Tidak Lulus">Tidak Lulus</option>
                            </select>
                            <p v-if="form.errors.status_kelulusan" class="text-red-500 text-xs mt-1">{{ form.errors.status_kelulusan }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai Ujian: Tulis</label>
                            <input v-model="form.nilai_ujian_tulis" type="number" min="0" max="100" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300" placeholder="Skala 0-100" />
                            <p v-if="form.errors.nilai_ujian_tulis" class="text-red-500 text-xs mt-1">{{ form.errors.nilai_ujian_tulis }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai Ujian: Materi</label>
                            <input v-model="form.nilai_ujian_materi" type="number" min="0" max="100" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300" placeholder="Skala 0-100" />
                            <p v-if="form.errors.nilai_ujian_materi" class="text-red-500 text-xs mt-1">{{ form.errors.nilai_ujian_materi }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai Ujian: Praktek Kelas</label>
                            <input v-model="form.nilai_ujian_praktek_kelas" type="number" min="0" max="100" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 border-gray-300" placeholder="Skala 0-100" />
                            <p v-if="form.errors.nilai_ujian_praktek_kelas" class="text-red-500 text-xs mt-1">{{ form.errors.nilai_ujian_praktek_kelas }}</p>
                        </div>

                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Data' }}
                    </button>
                    <Link :href="route('santris.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
