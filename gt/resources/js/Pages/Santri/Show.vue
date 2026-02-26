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
        haliyah_jabatan: string | null;
        haliyah_keaktifan: 'A' | 'B' | 'C' | null;
        haliyah_pelanggaran: string | null;
        akademisi: 'A' | 'B' | 'C' | null;
        kelas_formal: string | null;
        nilai_ujian_tulis: number | null;
        nilai_ujian_materi: number | null;
        nilai_ujian_praktek_kelas: number | null;
        marhalah_alquran: 'A' | 'B' | 'C' | null;
        status_seleksi: string;
        jumlah_nilai_praktek: number;
        rata_rata_nilai_praktek: number;
        created_at: string;
        updated_at: string;
        kamar?: string | null;
        status_kelulusan?: string | null;
        skills?: any[];
        penugasanAktif?: any;
        penugasans?: any[];
    };
    availableSkills: any[];
}>();

import { useForm } from '@inertiajs/vue3';

const formSkill = useForm({
    skill_id: '',
    level: 'dasar',
    keterangan: '',
});

const formSkillVisible = ref(false);

const addSkill = () => {
    formSkill.post(route('santri-skills.store', props.santri.id), {
        preserveScroll: true,
        onSuccess: () => {
            formSkill.reset();
            formSkillVisible.value = false;
        },
    });
};

const removeSkill = (skill: any) => {
    if (confirm(`Hapus skill '${skill.nama}' dari santri ini?`)) {
        router.delete(route('santri-skills.destroy', [props.santri.id, skill.id]), {
            preserveScroll: true,
        });
    }
};

const levelColor: Record<string, string> = {
    dasar:    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    menengah: 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    mahir:    'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
};

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
        <div class="space-y-6 w-full">

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
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Kamar</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.kamar || '—' }}</p>
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
                                
                                <div v-if="santri.penugasanAktif" class="mt-3 p-3 bg-indigo-50 border border-indigo-100 dark:bg-indigo-900/20 dark:border-indigo-800/30 rounded-lg">
                                    <p class="text-xs font-semibold text-indigo-800 dark:text-indigo-400 mb-1">Ditempatkan Di:</p>
                                    <Link :href="route('penugasans.show', santri.penugasanAktif.id)" class="text-sm font-medium text-indigo-700 hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-100 hover:underline">
                                        {{ santri.penugasanAktif.lembaga?.nama }} 
                                        ({{ santri.penugasanAktif.tahun_psm?.judul ?? 'Tanpa Periode' }})
                                    </Link>
                                    <p class="text-xs text-indigo-600 dark:text-indigo-400/80 mt-1 capitalize">{{ santri.penugasanAktif.status }}</p>
                                </div>

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

                    <!-- Panel: Keahlian (Skill) -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Keahlian (Skill) Santri</h2>
                            <button @click="formSkillVisible = !formSkillVisible" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                                {{ formSkillVisible ? 'Tutup Form' : '+ Tambah Skill' }}
                            </button>
                        </div>
                        
                        <!-- Form Tambah Skill -->
                        <div v-if="formSkillVisible" class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3 border border-gray-100 dark:border-gray-600">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Keahlian</label>
                                <select v-model="formSkill.skill_id" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm text-sm focus:ring-indigo-500 border-gray-300">
                                    <option value="" disabled>-- Pilih --</option>
                                    <option v-for="sk in availableSkills" :key="sk.id" :value="sk.id">{{ sk.nama }} ({{ sk.kategori }})</option>
                                </select>
                                <p v-if="formSkill.errors.skill_id" class="text-xs text-red-500 mt-0.5">{{ formSkill.errors.skill_id }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkatan (Level)</label>
                                <select v-model="formSkill.level" class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm text-sm focus:ring-indigo-500 border-gray-300">
                                    <option value="dasar">Dasar (Basic)</option>
                                    <option value="menengah">Menengah (Intermediate)</option>
                                    <option value="mahir">Mahir (Advanced)</option>
                                </select>
                                <p v-if="formSkill.errors.level" class="text-xs text-red-500 mt-0.5">{{ formSkill.errors.level }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan (Opsional)</label>
                                <input v-model="formSkill.keterangan" type="text" placeholder="cth: Hafalan mutqin 10 juz..." class="input dark:bg-gray-700 dark:text-white dark:border-gray-600 block w-full rounded-md shadow-sm text-sm border-gray-300">
                            </div>
                            <div class="pt-2">
                                <button @click="addSkill" :disabled="formSkill.processing" class="w-full px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                                    {{ formSkill.processing ? 'Menyimpan...' : 'Simpan Skill' }}
                                </button>
                            </div>
                        </div>

                        <!-- Daftar Skill -->
                        <div v-if="santri.skills && santri.skills.length > 0" class="space-y-2">
                            <div v-for="sk in santri.skills" :key="sk.id" class="flex items-center justify-between p-3 border border-gray-100 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ sk.nama }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold uppercase rounded" :class="levelColor[sk.pivot.level]">
                                            {{ sk.pivot.level }}
                                        </span>
                                        <span v-if="sk.pivot.keterangan" class="text-xs text-gray-500 truncate max-w-[200px]">{{ sk.pivot.keterangan }}</span>
                                    </div>
                                </div>
                                <button @click="removeSkill(sk)" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Hapus Skill">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-sm text-center py-6 text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                            Belum ada skill yang ditambahkan.
                        </div>
                    </div>

                    <!-- Panel Evaluasi & Seleksi -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Evaluasi & Seleksi</h2>
                            <div class="flex items-center gap-2">
                                <span :class="{'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': santri.status_kelulusan === 'Lulus', 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': santri.status_kelulusan === 'Tidak Lulus', 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-400': santri.status_kelulusan === 'Belum Lulus'}" class="px-2.5 py-0.5 rounded-full text-xs font-medium border border-gray-200 dark:border-gray-700">
                                    {{ santri.status_kelulusan || 'Belum Lulus' }}
                                </span>
                                <span :class="{'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': santri.status_seleksi === 'Lolos Tahap Akhir', 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': santri.status_seleksi === 'Lolos Tahap Awal', 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': santri.status_seleksi === 'Tidak Lolos', 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-400': santri.status_seleksi === 'Belum Diproses'}" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ santri.status_seleksi || 'Belum Diproses' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                            <!-- Kolom 1 -->
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Kelas Formal</p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.kelas_formal || '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Nilai Akademisi</p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.akademisi || '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Tingkat Baca Al-Quran (Marhalah)</p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.marhalah_alquran || '—' }}</p>
                                </div>
                                <div class="pt-2">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 border-b border-gray-100 dark:border-gray-700 pb-1">Haliyah (Perilaku)</p>
                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase">Jabatan</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.haliyah_jabatan || '—' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase">Keaktifan</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ santri.haliyah_keaktifan || '—' }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase">Catatan Pelanggaran</p>
                                        <p class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/10 p-2 rounded mt-1">{{ santri.haliyah_pelanggaran || 'Tidak Ada Pelanggaran' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kolom 2 -->
                            <div>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b border-gray-100 dark:border-gray-700 pb-1">Nilai Ujian Praktek</p>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/30 p-2 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Tulis</span>
                                        <span class="font-mono font-medium text-gray-900 dark:text-white">{{ santri.nilai_ujian_tulis ?? '0' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/30 p-2 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Materi</span>
                                        <span class="font-mono font-medium text-gray-900 dark:text-white">{{ santri.nilai_ujian_materi ?? '0' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/30 p-2 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Praktek / Penilaian Kelas</span>
                                        <span class="font-mono font-medium text-gray-900 dark:text-white">{{ santri.nilai_ujian_praktek_kelas ?? '0' }}</span>
                                    </div>
                                    
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600 space-y-2 mt-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-semibold text-gray-500 uppercase">Total Jumlah</span>
                                            <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ santri.jumlah_nilai_praktek }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-semibold text-gray-500 uppercase">Nilai Rata-rata</span>
                                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-lg">{{ santri.rata_rata_nilai_praktek }}</span>
                                        </div>
                                    </div>
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

            <!-- Riwayat Penugasan -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Riwayat Penugasan</h2>
                
                <div v-if="santri.penugasans && santri.penugasans.length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode Tugas</th>
                                <th class="px-4 py-3 text-left">Lembaga Tujuan</th>
                                <th class="px-4 py-3 text-left">Wilayah</th>
                                <th class="px-4 py-3 text-center">Periode / Tahun</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="tugas in santri.penugasans" :key="tugas.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-gray-600 dark:text-gray-300 font-medium">{{ tugas.kode_tugas || '—' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Link :href="route('lembagas.show', tugas.lembaga_id)" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ tugas.lembaga?.nama }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ tugas.lembaga?.wilayah?.nama ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                    {{ tugas.tahun_psm?.judul ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full" 
                                        :class="tugas.status === 'diusulkan' ? 'bg-yellow-100 text-yellow-800' : 
                                                tugas.status === 'disetujui' ? 'bg-blue-100 text-blue-800' :
                                                tugas.status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 
                                                tugas.status === 'selesai' ? 'bg-gray-100 text-gray-700' : 
                                                'bg-red-100 text-red-700'">
                                        {{ tugas.status.charAt(0).toUpperCase() + tugas.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link :href="route('penugasans.show', tugas.id)" class="text-xs text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                                        Detail
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-6 text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    Belum ada riwayat penugasan untuk santri ini.
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
