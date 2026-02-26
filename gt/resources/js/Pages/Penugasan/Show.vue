<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
    penugasan: any;
    reports: any[];
}>();

const searchTerm = ref('');
const typeFilter = ref('all');
const statusFilter = ref('all');

const periodLabels: Record<number, string> = {
    1: "Syawal - Dzulqo'dah",
    2: "Dzulqo'dah - Dzulhijjah",
    3: "Dzulhijjah - Muhram",
    4: "Muharam - Safar",
    5: "Safar - Rabi'ul Awal",
    6: "Rabi'ul Awal - Rabi'ul Akhir",
    7: "Rabi'ul Akhir - Jumadil Awal",
    8: "Jumadil Awal - Jumadil Akhir",
    9: "Jumadil Akhir - Rajab",
    10: "Rajab - Sya'ban",
};

const typeLabels: Record<string, string> = {
    gt: 'GT', pjgt: 'PJGT', korwil: 'Korwil'
};

const typeColors: Record<string, string> = {
    gt: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    pjgt: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    korwil: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
};

const statusLabels: Record<string, string> = {
    draft: 'Draft', submitted: 'Terkirim', evaluated: 'Dievaluasi'
};

const reportStatusColor: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
    submitted: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
    evaluated: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
};

const filteredReports = computed(() => {
    return props.reports.filter(r => {
        const matchesSearch = searchTerm.value === '' || 
            (r.reporter_name?.toLowerCase().includes(searchTerm.value.toLowerCase()) || 
             r.status?.toLowerCase().includes(searchTerm.value.toLowerCase()));
        
        const matchesType = typeFilter.value === 'all' || r.report_type === typeFilter.value;
        const matchesStatus = statusFilter.value === 'all' || r.status === statusFilter.value;
        
        return matchesSearch && matchesType && matchesStatus;
    });
});

const groupedReports = computed(() => {
    const groups: Record<string, any[]> = {};
    filteredReports.value.forEach(r => {
        const key = `${r.period_month}-${r.period_year}`;
        if (!groups[key]) groups[key] = [];
        groups[key].push(r);
    });
    
    // Sort keys descending (year then month)
    return Object.keys(groups).sort((a, b) => {
        const [mA, yA] = a.split('-').map(Number);
        const [mB, yB] = b.split('-').map(Number);
        if (yB !== yA) return yB - yA;
        return mB - mA;
    }).map(key => ({
        key,
        period: key,
        month: Number(key.split('-')[0]),
        year: Number(key.split('-')[1]),
        items: groups[key]
    }));
});

const statusColor: Record<string, string> = {
    diusulkan:  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    disetujui:  'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    aktif:      'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
    selesai:    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    dibatalkan: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
};

const labelStatus: Record<string, string> = {
    diusulkan: 'Diusulkan', disetujui: 'Disetujui', aktif: 'Aktif',
    selesai: 'Selesai', dibatalkan: 'Dibatalkan',
};

const levelColor: Record<string, string> = {
    dasar:    'bg-gray-100 text-gray-600',
    menengah: 'bg-blue-50 text-blue-600',
    mahir:    'bg-purple-50 text-purple-600',
};

const confirmStatus = ref<string | null>(null);

const ubahStatus = (newStatus: string) => {
    confirmStatus.value = newStatus;
};

const doUbahStatus = () => {
    if (confirmStatus.value) {
        router.post(route('penugasans.ubah-status', props.penugasan.id), { status: confirmStatus.value }, {
            onSuccess: () => (confirmStatus.value = null)
        });
    }
};

const nextStatuses: Record<string, { label: string; status: string; color: string }[]> = {
    diusulkan:  [{ label: 'Setujui', status: 'disetujui', color: 'bg-blue-600 hover:bg-blue-700 text-white' }, { label: 'Batalkan', status: 'dibatalkan', color: 'bg-red-50 hover:bg-red-100 text-red-600' }],
    disetujui:  [{ label: 'Aktifkan', status: 'aktif', color: 'bg-emerald-600 hover:bg-emerald-700 text-white' }, { label: 'Batalkan', status: 'dibatalkan', color: 'bg-red-50 hover:bg-red-100 text-red-600' }],
    aktif:      [{ label: 'Selesaikan', status: 'selesai', color: 'bg-gray-600 hover:bg-gray-700 text-white' }],
    selesai:    [],
    dibatalkan: [{ label: 'Usulkan Lagi', status: 'diusulkan', color: 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700' }],
};
</script>

<template>
    <Head :title="`Penugasan – ${penugasan.santri?.nama}`" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-5">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link :href="route('penugasans.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Penugasan</h1>
                            <span v-if="penugasan.kode_tugas" class="px-2.5 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-md text-xs font-mono font-medium border border-gray-200 dark:border-gray-600">
                                {{ penugasan.kode_tugas }}
                            </span>
                        </div>
                        <span class="inline-block mt-1 px-3 py-0.5 text-sm font-medium rounded-full" :class="statusColor[penugasan.status]">
                            {{ labelStatus[penugasan.status] }}
                        </span>
                    </div>
                </div>

                <!-- Aksi status -->
                <div class="flex items-center gap-2 flex-wrap">
                    <button v-for="act in nextStatuses[penugasan.status]" :key="act.status"
                        @click="ubahStatus(act.status)"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                        :class="act.color">
                        {{ act.label }}
                    </button>
                    <Link :href="route('penugasans.edit', penugasan.id)"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Edit
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- Data Santri & Lembaga -->
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Santri Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 space-y-4">
                        <h2 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Data Guru Tugas</h2>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-2xl">
                                {{ penugasan.santri?.nama?.[0] }}
                            </div>
                            <div>
                                <Link :href="route('santris.show', penugasan.santri?.id)"
                                    class="font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 text-xl decoration-indigo-200 hover:underline decoration-2 leading-tight">
                                    {{ penugasan.santri?.nama }}
                                </Link>
                                <p class="text-xs font-medium text-gray-400 mt-0.5">NIS: {{ penugasan.santri?.nis }}</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Skill yang Dimiliki</p>
                            <div v-if="penugasan.santri?.skills?.length > 0" class="flex flex-wrap gap-2">
                                <span v-for="sk in penugasan.santri.skills" :key="sk.id"
                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold border border-transparent shadow-sm"
                                    :class="levelColor[sk.pivot?.level ?? 'dasar']">
                                    {{ sk.nama }}
                                    <span class="ml-1 opacity-50 font-normal">· {{ sk.pivot?.level }}</span>
                                </span>
                            </div>
                            <p v-else class="text-sm text-gray-400 italic">Belum ada skill terdaftar.</p>
                        </div>
                    </div>

                    <!-- Lembaga Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 space-y-4">
                        <h2 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Lembaga Tujuan</h2>

                        <div>
                            <Link :href="route('lembagas.show', penugasan.lembaga?.id)"
                                class="font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 text-xl decoration-indigo-200 hover:underline decoration-2">
                                {{ penugasan.lembaga?.nama }}
                            </Link>
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mt-1.5 font-medium">
                                <span>📍</span>
                                <span>{{ penugasan.lembaga?.wilayah?.nama ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 mt-3 p-2.5 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                <span>👤</span>
                                <span class="font-semibold">PJGT: <span class="text-gray-700 dark:text-gray-300 font-normal">{{ penugasan.lembaga?.pjgt?.nama ?? penugasan.lembaga?.wilayah?.pjgt?.nama ?? '—' }}</span></span>
                            </div>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Kebutuhan Skill</p>
                            <div v-if="penugasan.lembaga?.kebutuhans?.length > 0" class="flex flex-wrap gap-2">
                                <span v-for="k in penugasan.lembaga.kebutuhans" :key="k.id"
                                    class="px-3 py-1.5 rounded-xl text-xs font-semibold border shadow-sm"
                                    :class="k.prioritas === 'wajib' ? 'bg-red-50 text-red-700 border-red-100' : k.prioritas === 'diutamakan' ? 'bg-yellow-50 text-yellow-800 border-yellow-100' : 'bg-gray-50 text-gray-600 border-gray-100'">
                                    {{ k.skill?.nama }}
                                </span>
                            </div>
                            <p v-else class="text-sm text-gray-400 italic">Belum ada kebutuhan skill.</p>
                        </div>
                    </div>
                </div>

                <!-- Detail & Timeline Sidebar -->
                <div class="space-y-5">
                    <!-- Skor & Waktu -->
                    <div class="bg-indigo-600 text-white rounded-2xl shadow-lg p-6 relative overflow-hidden group">
                        <!-- Decorative circle -->
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-colors"></div>
                        
                        <h2 class="text-xs font-bold text-indigo-100 uppercase tracking-widest mb-4 opacity-80">Info Penugasan</h2>
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-end justify-between border-b border-indigo-500/50 pb-3">
                                <span class="text-sm text-indigo-100 font-medium">Skor Kecocokan</span>
                                <span class="text-4xl font-black leading-none tracking-tighter">{{ penugasan.skor_kecocokan }}%</span>
                            </div>
                            
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-indigo-100 opacity-80">Tahun PSM</span>
                                    <span class="font-bold">{{ penugasan.tahun_psm?.judul ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-indigo-100 opacity-80">Mulai</span>
                                    <span class="font-bold">
                                        {{ penugasan.tanggal_mulai ? new Date(penugasan.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : (penugasan.tahun_psm?.tanggal_mulai_masehi ? new Date(penugasan.tahun_psm.tanggal_mulai_masehi).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '—') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-indigo-100 opacity-80">Selesai</span>
                                    <span class="font-bold">
                                        {{ penugasan.tanggal_selesai ? new Date(penugasan.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : (penugasan.tahun_psm?.tanggal_selesai_masehi ? new Date(penugasan.tahun_psm.tanggal_selesai_masehi).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '—') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta / Timeline -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                        <h2 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Timeline</h2>
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 w-2 h-2 rounded-full bg-indigo-500 ring-4 ring-indigo-50 dark:ring-indigo-900/20"></div>
                                <div>
                                    <p class="text-xs font-bold text-gray-900 dark:text-white">Dibuat</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ new Date(penugasan.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
                                </div>
                            </div>
                            <div v-if="penugasan.disetujui_pada" class="flex items-start gap-4">
                                <div class="mt-1 w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-50 dark:ring-emerald-900/20"></div>
                                <div>
                                    <p class="text-xs font-bold text-gray-900 dark:text-white">Disetujui</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ new Date(penugasan.disetujui_pada).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                                    <p v-if="penugasan.disetujui_oleh" class="text-[9px] text-gray-400 mt-1">Oleh: {{ penugasan.disetujui_oleh?.name }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="penugasan.catatan" class="mt-5 pt-5 border-t border-gray-50 dark:border-gray-700">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Catatan</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed italic">"{{ penugasan.catatan }}"</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Terhubung</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Daftar laporan dari GT, PJGT, dan Korwil untuk penugasan ini.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative">
                            <input v-model="searchTerm" type="text" placeholder="Cari laporan..." 
                                class="pl-9 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 w-full md:w-64">
                            <span class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </div>
                        
                        <select v-model="typeFilter" class="bg-gray-50 dark:bg-gray-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-w-[100px]">
                            <option value="all">Semua Tipe</option>
                            <option value="gt">GT</option>
                            <option value="pjgt">PJGT</option>
                            <option value="korwil">Korwil</option>
                        </select>

                        <select v-model="statusFilter" class="bg-gray-50 dark:bg-gray-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-w-[120px]">
                            <option value="all">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Terkirim</option>
                            <option value="evaluated">Dievaluasi</option>
                        </select>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="groupedReports.length > 0" class="space-y-8">
                        <div v-for="group in groupedReports" :key="group.key">
                            <div class="flex items-center gap-4 mb-4">
                                <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest bg-indigo-50 dark:bg-indigo-900/20 px-3 py-1 rounded-md">
                                    {{ periodLabels[group.month] || 'Periode ' + group.month }} {{ group.year }} H
                                </h3>
                                <div class="flex-grow h-px bg-gray-100 dark:bg-gray-700"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                <div v-for="report in group.items" :key="report.id" 
                                    class="group p-5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-800 rounded-2xl transition-all hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" :class="typeColors[report.report_type]">
                                                {{ typeLabels[report.report_type] }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold" :class="reportStatusColor[report.status]">
                                                {{ statusLabels[report.status] }}
                                            </span>
                                        </div>
                                        <Link :href="route('reports.edit', report.id)" 
                                            class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ report.reporter_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ new Date(report.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center text-gray-300 dark:text-gray-700 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Tidak ada laporan ditemukan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada laporan yang tercatat atau tidak sesuai filter.</p>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmModal
            :show="!!confirmStatus"
            :title="'Konfirmasi Ubah Status'"
            :message="`Ubah status menjadi '${confirmStatus ? labelStatus[confirmStatus] : ''}'?`"
            :confirmText="'Ya, Ubah'"
            :cancelText="'Batal'"
            :type="'info'"
            @confirm="doUbahStatus"
            @cancel="confirmStatus = null"
        />
    </AuthenticatedLayout>
</template>
