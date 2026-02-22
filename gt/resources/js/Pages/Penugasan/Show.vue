<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    penugasan: any;
}>();

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
        <div class="max-w-4xl space-y-5">
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <!-- Santri Info -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 space-y-4">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data Santri / Guru Tugas</h2>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                            {{ penugasan.santri?.nama?.[0] }}
                        </div>
                        <div>
                            <Link :href="route('santris.show', penugasan.santri?.id)"
                                class="font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 text-base">
                                {{ penugasan.santri?.nama }}
                            </Link>
                            <p class="text-xs text-gray-400">NIS: {{ penugasan.santri?.nis }}</p>
                        </div>
                    </div>

                    <!-- Skill santri -->
                    <div>
                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-2">Skill yang Dimiliki</p>
                        <div v-if="penugasan.santri?.skills?.length > 0" class="flex flex-wrap gap-1.5">
                            <span v-for="sk in penugasan.santri.skills" :key="sk.id"
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="levelColor[sk.pivot?.level ?? 'dasar']">
                                {{ sk.nama }}
                                <span class="opacity-60">· {{ sk.pivot?.level }}</span>
                            </span>
                        </div>
                        <p v-else class="text-sm text-gray-400 italic">Belum ada skill terdaftar.</p>
                    </div>
                </div>

                <!-- Lembaga Info -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 space-y-4">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Lembaga Tujuan</h2>

                    <div>
                        <Link :href="route('lembagas.show', penugasan.lembaga?.id)"
                            class="font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 text-base">
                            {{ penugasan.lembaga?.nama }}
                        </Link>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">📍 {{ penugasan.lembaga?.wilayah?.nama ?? '—' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            👤 PJGT: <span class="font-medium text-gray-700 dark:text-gray-300">{{ penugasan.lembaga?.pjgt?.nama ?? penugasan.lembaga?.wilayah?.pjgt?.nama ?? '—' }}</span>
                        </p>
                    </div>

                    <!-- Kebutuhan lembaga -->
                    <div>
                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-2">Kebutuhan Skill Lembaga</p>
                        <div v-if="penugasan.lembaga?.kebutuhans?.length > 0" class="flex flex-wrap gap-1.5">
                            <span v-for="k in penugasan.lembaga.kebutuhans" :key="k.id"
                                class="px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="k.prioritas === 'wajib' ? 'bg-red-50 text-red-600' : k.prioritas === 'diutamakan' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-100 text-gray-500'">
                                {{ k.skill?.nama }} ({{ k.prioritas }})
                            </span>
                        </div>
                        <p v-else class="text-sm text-gray-400 italic">Belum ada kebutuhan skill.</p>
                    </div>
                </div>

                <!-- Skor & Waktu -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Detail Penugasan</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Skor Kecocokan</span>
                            <span class="font-bold text-xl text-indigo-600 dark:text-indigo-400">{{ penugasan.skor_kecocokan }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Periode / Tahun PSM</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ penugasan.tahun_psm?.judul ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Mulai</span>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ penugasan.tanggal_mulai ? new Date(penugasan.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : (penugasan.tahun_psm?.tanggal_mulai_masehi ? new Date(penugasan.tahun_psm.tanggal_mulai_masehi).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '—') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Selesai</span>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ penugasan.tanggal_selesai ? new Date(penugasan.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : (penugasan.tahun_psm?.tanggal_selesai_masehi ? new Date(penugasan.tahun_psm.tanggal_selesai_masehi).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '—') }}
                            </span>
                        </div>
                        <div v-if="penugasan.disetujui_oleh" class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Disetujui Oleh</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ penugasan.disetujui_oleh?.name }}</span>
                        </div>
                        <div v-if="penugasan.catatan" class="pt-2 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400 mb-1">Catatan</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ penugasan.catatan }}</p>
                        </div>
                    </div>
                </div>

                <!-- Meta -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Timeline</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                            <span>📋</span>
                            <span>Dibuat: {{ new Date(penugasan.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</span>
                        </div>
                        <div v-if="penugasan.disetujui_pada" class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                            <span>✅</span>
                            <span>Disetujui: {{ new Date(penugasan.disetujui_pada).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                        </div>
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
