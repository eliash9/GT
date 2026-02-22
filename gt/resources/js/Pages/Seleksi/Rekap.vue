<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    stats_seleksi: Record<string, number>;
    stats_kelulusan: Record<string, number>;
    top_santris: any[];
}>();

const totalSantri = Object.values(props.stats_seleksi).reduce((a, b) => a + b, 0);
const persenLolos = totalSantri > 0 ? Math.round(((props.stats_seleksi['Lolos Tahap Akhir'] || 0) / totalSantri) * 100) : 0;
</script>

<template>
    <Head title="Rekap Hasil Seleksi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Rekap Hasil Seleksi
                </h2>
                <Link
                    :href="route('seleksi.index')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-50 dark:bg-indigo-900 border border-transparent rounded-md font-semibold text-xs text-indigo-700 dark:text-indigo-300 uppercase tracking-widest hover:bg-indigo-100 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150"
                >
                    Kembali ke Tabel Massal
                </Link>
            </div>
        </template>

        <div class="max-w-7xl mx-auto py-8 space-y-8">
            <!-- Statistik Utama -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card Lolos Akhir -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-emerald-100 dark:border-emerald-900/50 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-emerald-50 dark:bg-emerald-900/20 z-0"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400 mb-1">Total Lolos Tahap Akhir</p>
                        <h3 class="text-4xl font-bold text-gray-900 dark:text-white">{{ stats_seleksi['Lolos Tahap Akhir'] || 0 }}</h3>
                        <p class="text-xs text-gray-500 mt-2">{{ persenLolos }}% dari total {{ totalSantri }} Santri</p>
                    </div>
                </div>

                <!-- Card Kelulusan -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-blue-100 dark:border-blue-900/50 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-blue-50 dark:bg-blue-900/20 z-0"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">Status Kelulusan</p>
                        <div class="mt-2 space-y-1">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Lulus</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ stats_kelulusan['Lulus'] || 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Belum Lulus</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ stats_kelulusan['Belum Lulus'] || 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Tidak Lulus</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ stats_kelulusan['Tidak Lulus'] || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Seleksi Awal -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-gray-50 dark:bg-gray-700/50 z-0"></div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Progres Proses</p>
                        <div class="mt-2 space-y-1">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Belum Diproses</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ stats_seleksi['Belum Diproses'] || 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Lolos Tahap Awal</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ stats_seleksi['Lolos Tahap Awal'] || 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Tidak Lolos</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ stats_seleksi['Tidak Lolos'] || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Table Top 10 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top 10 Santri Predikat Terbaik</h3>
                    <p class="text-xs text-gray-500">Hanya menampilkan Santri yang "Lulus" dan "Lolos Tahap Akhir" diurutkan berdasarkan Nilai Rata-rata Ujian Praktek.</p>
                </div>
                
                <div v-if="top_santris.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-white dark:bg-gray-800">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16 text-center">Rank</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Haliyah</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Akademisi & Quran</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400">Rata-rata Ujian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="(item, index) in top_santris" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span v-if="index === 0" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100 text-yellow-800 font-bold mb-1 shadow-sm text-lg border border-yellow-200">1</span>
                                    <span v-else-if="index === 1" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-gray-800 font-bold mb-1 shadow-sm text-lg border border-gray-300">2</span>
                                    <span v-else-if="index === 2" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-800 font-bold mb-1 shadow-sm text-lg border border-amber-200">3</span>
                                    <span v-else class="text-gray-500 font-semibold">{{ index + 1 }}</span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ item.nama }}</div>
                                    <div class="text-xs text-gray-500">{{ item.kelas_formal || '—' }} | NIS: {{ item.nis }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">{{ item.haliyah_jabatan || 'Santri Biasa' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Keaktifan: <span class="font-bold text-indigo-600">{{ item.haliyah_keaktifan || '—' }}</span></div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex space-x-4">
                                        <div title="Nilai Akademik">
                                            <span class="block text-[10px] text-gray-400 uppercase">Akdm</span>
                                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ item.akademisi || '—' }}</span>
                                        </div>
                                        <div title="Tingkat Quran">
                                            <span class="block text-[10px] text-gray-400 uppercase">Qrn</span>
                                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ item.marhalah_alquran || '—' }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center bg-indigo-50/50 dark:bg-indigo-900/10">
                                    <span class="inline-flex px-3 py-1 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-400 font-bold text-lg border border-indigo-200 dark:border-indigo-800/50">
                                        {{ item.rata_rata_nilai_praktek }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-else class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum Ada Rekap Data</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pastikan ada Santri yang Lolos Tahap Akhir dan dinyatakan Lulus terlebih dahulu.</p>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
