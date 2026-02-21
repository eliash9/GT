<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps<{
    santri: {
        id: number;
        nis: string;
        nama: string;
        jenis_kelamin: 'L' | 'P';
        tempat_lahir: string;
        tanggal_lahir: string;
        alamat_lengkap: string | null;
        no_hp: string | null;
        angkatan: number;
        status_tugas: string;
        foto: string | null;
        kelas: string | null;
        nama_ayah: string | null;
        created_at: string;
        updated_at: string;
    };
}>();

const confirmDelete = ref(false);
const doDelete = () => {
    router.delete(route('santris.destroy', props.santri.id), {
        onSuccess: () => confirmDelete.value = false,
    });
};

// Computed
const fotoUrl = computed(() =>
    props.santri.foto ? '/storage/' + props.santri.foto : null
);

const initial = computed(() =>
    props.santri.nama?.charAt(0)?.toUpperCase() ?? '?'
);

const usia = computed(() => {
    const dob = new Date(props.santri.tanggal_lahir);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    return age;
});

const tglLahirFormatted = computed(() => {
    return new Date(props.santri.tanggal_lahir).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
});

const statusConfig = computed(() => {
    const s = props.santri.status_tugas;
    if (s === 'Menunggu')        return { color: 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',   dot: 'bg-amber-500'  };
    if (s === 'Sedang Bertugas') return { color: 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',       dot: 'bg-blue-500'   };
    return                             { color: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', dot: 'bg-emerald-500' };
});

const tglDibuat = computed(() =>
    new Date(props.santri.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
);
const tglUpdate = computed(() =>
    new Date(props.santri.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
);
</script>

<template>
    <Head :title="`Santri – ${santri.nama}`" />
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-5xl">

            <!-- ── Header ──────────────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('santris.index')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ santri.nama }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">NIS: {{ santri.nis }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('santris.edit', santri.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </Link>
                    <button
                        @click="confirmDelete = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>

            <!-- ── Body Grid ───────────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT: Foto + Status -->
                <div class="space-y-4">

                    <!-- Foto / Avatar -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center gap-4">
                        <!-- Photo -->
                        <div class="relative">
                            <img
                                v-if="fotoUrl"
                                :src="fotoUrl"
                                :alt="santri.nama"
                                class="h-28 w-28 rounded-full object-cover ring-4 ring-indigo-100 dark:ring-indigo-900/40"
                            />
                            <div
                                v-else
                                class="h-28 w-28 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 ring-4 ring-indigo-100 dark:ring-indigo-900/40 flex items-center justify-center text-white text-4xl font-bold"
                            >
                                {{ initial }}
                            </div>
                            <!-- Gender badge -->
                            <span
                                class="absolute bottom-1 right-1 flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold text-white shadow-md"
                                :class="santri.jenis_kelamin === 'L' ? 'bg-blue-500' : 'bg-pink-500'"
                            >
                                {{ santri.jenis_kelamin === 'L' ? '♂' : '♀' }}
                            </span>
                        </div>

                        <div class="text-center">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ santri.nama }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ santri.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>

                        <!-- Status badge -->
                        <span
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold"
                            :class="statusConfig.color"
                        >
                            <span class="w-2 h-2 rounded-full" :class="statusConfig.dot" />
                            {{ santri.status_tugas }}
                        </span>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ santri.angkatan }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Angkatan</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ usia }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tahun</p>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="text-xs text-gray-400 dark:text-gray-500 px-1 space-y-1">
                        <p>📅 Ditambahkan: {{ tglDibuat }}</p>
                        <p>🔄 Diperbarui: {{ tglUpdate }}</p>
                    </div>
                </div>

                <!-- RIGHT: Detail Info -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Identitas -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Identitas Diri</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Nomor Induk Santri (NIS)</p>
                                <p class="text-sm font-semibold font-mono text-gray-800 dark:text-gray-200">{{ santri.nis }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Kelas</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.kelas || '—' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Tempat Lahir</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.tempat_lahir }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Tanggal Lahir</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ tglLahirFormatted }}
                                    <span class="text-xs text-gray-400 ml-1">({{ usia }} tahun)</span>
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Nama Ayah / Wali</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.nama_ayah || '—' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Nomor HP</p>
                                <div v-if="santri.no_hp" class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.no_hp }}</p>
                                    <a
                                        :href="`https://wa.me/${santri.no_hp.replace(/\D/g, '').replace(/^0/, '62')}`"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 hover:underline"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                                        </svg>
                                        WhatsApp
                                    </a>
                                </div>
                                <p v-else class="text-sm text-gray-400 italic">—</p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Alamat Lengkap</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-relaxed">
                                    {{ santri.alamat_lengkap || '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Penugasan detail -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Status Penugasan</h2>
                        <div class="flex items-start gap-4">
                            <!-- Timeline dot -->
                            <div class="flex flex-col items-center gap-1 mt-1 shrink-0">
                                <div class="w-3 h-3 rounded-full" :class="statusConfig.dot" />
                                <div class="w-px flex-1 bg-gray-200 dark:bg-gray-700 min-h-[40px]" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ santri.status_tugas }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    <template v-if="santri.status_tugas === 'Menunggu'">
                                        Santri belum mendapatkan penugasan ke lembaga manapun.
                                    </template>
                                    <template v-else-if="santri.status_tugas === 'Sedang Bertugas'">
                                        Santri sedang aktif menjalankan tugas sebagai Guru Tugas.
                                    </template>
                                    <template v-else>
                                        Santri telah menyelesaikan masa tugasnya.
                                    </template>
                                </p>
                                <!-- Quick change status -->
                                <div class="flex gap-2 mt-3">
                                    <Link
                                        :href="route('santris.edit', santri.id)"
                                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                                    >
                                        Ubah Status →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Angkatan Info -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-800/40 p-6">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-indigo-700 dark:text-indigo-400">Angkatan / Tahun Lulus</p>
                                <p class="text-3xl font-bold text-indigo-900 dark:text-indigo-200">{{ santri.angkatan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmDelete"
            :message="`Hapus data santri '${santri.nama}'? Data akan masuk ke tong sampah.`"
            @confirm="doDelete"
            @cancel="confirmDelete = false"
        />
    </AuthenticatedLayout>
</template>
