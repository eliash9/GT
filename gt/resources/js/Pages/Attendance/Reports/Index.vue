<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '../../../Components/Pagination.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    attendances: any;
    sessions: any[];
    filters: any;
}>();

const session_id = ref(props.filters.session_id || '');
const status = ref(props.filters.status || '');
const role = ref(props.filters.role || '');

const updateFilters = debounce(() => {
    router.get(route('attendance-reports.index'), {
        session_id: session_id.value,
        status: status.value,
        role: role.value,
    }, { preserveState: true, replace: true });
}, 300);

watch([session_id, status, role], () => {
    updateFilters();
});

const getLembaga = (user: any) => {
    const roleName = user.roles[0]?.name;
    if (roleName === 'gt') {
        return user.santri?.penugasan_aktif?.lembaga?.nama || 'Belum ditugaskan';
    } 
    if (roleName === 'pjgt' || roleName === 'korwil') {
        const count = user.pjgt?.lembagas?.length || 0;
        return `${count} Lembaga Tanggung Jawab`;
    }
    return '-';
};

const exportExcel = () => {
    window.location.href = route('attendance-reports.excel', {
        session_id: session_id.value,
        status: status.value,
        role: role.value,
    });
};

const exportPdf = () => {
    window.location.href = route('attendance-reports.pdf', {
        session_id: session_id.value,
        status: status.value,
        role: role.value,
    });
};
</script>

<template>
    <Head title="Laporan Absensi" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rekap & Laporan Absensi</h1>
                
                <div class="flex items-center gap-2">
                    <button @click="exportExcel" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm flex items-center gap-2 transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </button>
                    <button @click="exportPdf" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-sm flex items-center gap-2 transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Cetak PDF
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Sesi Kegiatan</label>
                        <select v-model="session_id" class="w-full rounded-xl border-gray-100 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-indigo-500 transition-all">
                            <option value="">Semua Sesi</option>
                            <option v-for="s in sessions" :key="s.id" :value="s.id">{{ s.title }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Filter Role</label>
                        <select v-model="role" class="w-full rounded-xl border-gray-100 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-indigo-500 transition-all">
                            <option value="">Semua Role</option>
                            <option value="gt">Guru Tugas (GT)</option>
                            <option value="pjgt">PJGT / Korwil</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Status Kehadiran</label>
                        <select v-model="status" class="w-full rounded-xl border-gray-100 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-indigo-500 transition-all">
                            <option value="">Semua Status</option>
                            <option value="present">Hadir Tepat Waktu</option>
                            <option value="late">Terlambat</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Scan</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Peserta</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Sesi Kegiatan</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Institusi / Tugas</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="att in attendances.data" :key="att.id" class="hover:bg-gray-50/30 transition-colors">
                                <td class="p-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ new Date(att.scanned_at).toLocaleDateString('id-ID') }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 uppercase font-bold">
                                        {{ new Date(att.scanned_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-black text-gray-900 dark:text-white">{{ att.user?.name }}</p>
                                    <span class="text-[10px] px-2 py-0.5 bg-gray-100 text-gray-500 rounded font-black uppercase tracking-tighter">
                                        {{ att.user?.roles[0]?.name }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ att.session?.title }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold leading-tight">
                                        {{ getLembaga(att.user) }}
                                    </p>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest"
                                        :class="att.status === 'present' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                                        {{ att.status === 'present' ? 'Hadir' : 'Terlambat' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="attendances.data.length === 0">
                                <td colspan="5" class="p-12 text-center text-gray-400 italic">Data absensi tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-50 bg-gray-50/30">
                    <Pagination :links="attendances.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
