<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import QrcodeVue from 'qrcode.vue';
import { ref } from 'vue';

const props = defineProps<{
    session: any;
    attendances: any[];
}>();

const toggleStatus = () => {
    router.post(route('attendance-sessions.toggle', props.session.id));
};

const getTypeLabel = (type: string) => {
    const labels: any = {
        departure: 'Keberangkatan',
        meeting: 'Rapat',
        event: 'Kegiatan Umum',
        other: 'Lainnya'
    };
    return labels[type] || type;
};
</script>

<template>
    <Head :title="`Sesi: ${session.title}`" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link :href="route('attendance-sessions.index')" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ session.title }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ getTypeLabel(session.type) }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="toggleStatus" 
                        class="px-4 py-2 rounded-lg font-medium transition-colors border"
                        :class="session.status === 'open' ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'">
                        {{ session.status === 'open' ? 'Tutup Sesi' : 'Buka Sesi' }}
                    </button>
                    <Link :href="route('attendance-sessions.scanner', session.id)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        Buka Scanner Admin
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Session QR (For User Scans) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 flex flex-col items-center justify-center text-center">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">QR Code Sesi</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tampilkan QR ini untuk di-scan oleh GT/PJGT melalui aplikasi PWA mereka.</p>
                    
                    <div class="p-6 bg-white rounded-2xl border-4 border-gray-50 shadow-inner">
                        <qrcode-vue :value="session.qr_code" :size="200" level="H" />
                    </div>
                    
                    <div class="mt-6 flex flex-col items-center">
                        <span class="text-xs font-mono bg-gray-100 px-3 py-1 rounded text-gray-500 uppercase tracking-widest">
                            {{ session.qr_code }}
                        </span>
                        <p class="mt-4 text-[10px] text-gray-400 max-w-xs uppercase font-bold tracking-widest">Metode: User Scan Admin</p>
                    </div>
                </div>

                <!-- Attendances List -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Kehadiran</h2>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-black">{{ attendances.length }} Peserta</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Peserta</th>
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Metode</th>
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Scan</th>
                                    <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="att in attendances" :key="att.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-xs text-gray-500">
                                                {{ att.user?.name[0] }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ att.user?.name }}</p>
                                                <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ att.user?.email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-100 px-2 py-0.5 rounded">
                                            {{ att.method === 'admin_scan_user' ? 'Admin Scan User' : 'User Scan Session' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ new Date(att.scanned_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest"
                                            :class="att.status === 'present' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                                            {{ att.status === 'present' ? 'Hadir' : 'Terlambat' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="attendances.length === 0">
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic text-sm">Belum ada peserta yang hadir.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
