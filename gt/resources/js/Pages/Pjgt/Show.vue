<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

interface Lembaga {
    id: number;
    nama: string;
    alamat: string | null;
    status: string;
    wilayah?: { nama: string } | null;
}

interface Wilayah {
    id: number;
    nama: string;
    lembagas?: Lembaga[];
}

const props = defineProps<{
    pjgt: {
        id: number;
        id_pjgt: string | null;
        nama: string;
        no_hp: string | null;
        created_at: string;
        updated_at: string;
        wilayahs?: Wilayah[];
        lembagas?: Lembaga[];
        user?: {
            id: number;
            name: string;
            email: string;
            roles?: { name: string }[];
        } | null;
    };
}>();

const confirmDelete = ref(false);
const doDelete = () => {
    router.delete(route('pjgts.destroy', props.pjgt.id), {
        onSuccess: () => confirmDelete.value = false,
    });
};

const initial = computed(() => props.pjgt.nama?.charAt(0)?.toUpperCase() ?? '?');

const totalLembagas = computed(() => props.pjgt.lembagas?.length ?? 0);
const totalWilayahs = computed(() => props.pjgt.wilayahs?.length ?? 0);
const lembagas_aktif = computed(() => props.pjgt.lembagas?.filter(l => l.status === 'aktif').length ?? 0);

const tglDibuat = computed(() =>
    new Date(props.pjgt.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
);
const tglUpdate = computed(() =>
    new Date(props.pjgt.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
);
</script>

<template>
    <Head :title="`PJGT – ${pjgt.nama}`" />
    <AuthenticatedLayout>
        <div class="space-y-6 w-full">

            <!-- ── Header ─────────────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('pjgts.index')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ pjgt.nama }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Penanggung Jawab Guru Tugas
                            <span v-if="pjgt.id_pjgt" class="ml-1 font-mono bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs">{{ pjgt.id_pjgt }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('pjgts.edit', pjgt.id)"
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

                <!-- LEFT: Profil PJGT -->
                <div class="space-y-4">

                    <!-- Avatar + Info Kontak -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center gap-4">
                        <!-- Avatar -->
                        <div
                            class="h-24 w-24 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 ring-4 ring-indigo-100 dark:ring-indigo-900/40 flex items-center justify-center text-white text-3xl font-bold"
                        >
                            {{ initial }}
                        </div>

                        <div class="text-center">
                            <p class="font-semibold text-gray-900 dark:text-white text-lg">{{ pjgt.nama }}</p>
                            <p v-if="pjgt.id_pjgt" class="text-xs text-gray-400 font-mono mt-0.5">ID: {{ pjgt.id_pjgt }}</p>
                        </div>

                        <!-- Contact -->
                        <div class="w-full space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <div v-if="pjgt.no_hp" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-400">Nomor HP</p>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ pjgt.no_hp }}</p>
                                </div>
                            </div>
                            <div v-if="pjgt.no_hp">
                                <a
                                    :href="`https://wa.me/${pjgt.no_hp.replace(/\D/g, '').replace(/^0/, '62')}`"
                                    target="_blank"
                                    class="flex items-center justify-center gap-2 w-full py-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-sm font-medium hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                                    </svg>
                                    Hubungi via WhatsApp
                                </a>
                            </div>
                            <p v-else class="text-sm text-gray-400 italic text-center">Belum ada nomor kontak</p>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ totalWilayahs }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Wilayah</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ totalLembagas }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Lembaga</p>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="text-xs text-gray-400 dark:text-gray-500 px-1 space-y-1">
                        <p>📅 Ditambahkan: {{ tglDibuat }}</p>
                        <p>🔄 Diperbarui: {{ tglUpdate }}</p>
                    </div>

                    <!-- Akun Login -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Akun Login</h3>
                            <Link
                                v-if="pjgt.user"
                                :href="route('users.edit', pjgt.user.id)"
                                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                            >Edit Akun →</Link>
                        </div>
                        <div v-if="pjgt.user" class="space-y-2">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                                <span class="text-xs font-mono text-gray-700 dark:text-gray-300 truncate">{{ pjgt.user.email }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span
                                    v-for="role in pjgt.user.roles"
                                    :key="role.name"
                                    class="inline-block px-1.5 py-0.5 text-[10px] font-semibold rounded bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 uppercase"
                                >{{ role.name }}</span>
                            </div>
                            <p class="text-xs text-gray-400">ID User: #{{ pjgt.user.id }}</p>
                        </div>
                        <div v-else class="text-xs text-amber-600 dark:text-amber-400 italic">
                            Belum terhubung ke akun user.
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Wilayah & Lembaga -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Wilayah yang dikelola -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Wilayah yang Dikelola</h2>
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full font-medium">
                                {{ totalWilayahs }} wilayah
                            </span>
                        </div>

                        <div v-if="pjgt.wilayahs && pjgt.wilayahs.length > 0" class="space-y-2">
                            <div
                                v-for="wilayah in pjgt.wilayahs"
                                :key="wilayah.id"
                                class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            >
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ wilayah.nama }}</p>
                                    <p class="text-xs text-gray-400">{{ wilayah.lembagas?.length ?? 0 }} lembaga</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <p class="text-sm">Belum ada wilayah yang ditugaskan</p>
                        </div>
                    </div>

                    <!-- Daftar Lembaga -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Lembaga Penerima GT</h2>
                            <div class="flex items-center gap-2">
                                <span class="text-xs bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded-full font-medium">
                                    {{ lembagas_aktif }} aktif
                                </span>
                                <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full font-medium">
                                    {{ totalLembagas }} total
                                </span>
                            </div>
                        </div>

                        <div v-if="pjgt.lembagas && pjgt.lembagas.length > 0" class="space-y-2">
                            <Link
                                v-for="lembaga in pjgt.lembagas"
                                :key="lembaga.id"
                                :href="route('lembagas.show', lembaga.id)"
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group"
                            >
                                <div
                                    class="w-2.5 h-2.5 rounded-full shrink-0"
                                    :class="lembaga.status === 'aktif' ? 'bg-emerald-500' : 'bg-red-400'"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                        {{ lembaga.nama }}
                                    </p>
                                    <p class="text-xs text-gray-400 truncate">
                                        {{ lembaga.wilayah?.nama || lembaga.alamat || '—' }}
                                    </p>
                                </div>
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full font-medium shrink-0"
                                    :class="lembaga.status === 'aktif'
                                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                        : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                                >
                                    {{ lembaga.status === 'aktif' ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 dark:text-gray-600 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-sm">Belum ada lembaga yang terdaftar</p>
                            <Link
                                :href="route('lembagas.create')"
                                class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline"
                            >
                                Tambah Lembaga →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmDelete"
            :message="`Hapus data PJGT '${pjgt.nama}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = false"
        />
    </AuthenticatedLayout>
</template>
