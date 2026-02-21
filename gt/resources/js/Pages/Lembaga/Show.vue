<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

// Leaflet loaded async to avoid SSR issues
const LeafletMap = defineAsyncComponent(() => import('@/Components/LeafletMap.vue'));

const props = defineProps<{
    lembaga: {
        id: number;
        nama: string;
        alamat: string | null;
        latitude: string | null;
        longitude: string | null;
        urlmap: string | null;
        status: string;
        wilayah_id: number | null;
        pjgt_id: number | null;
        created_at: string;
        updated_at: string;
        wilayah?: { id: number; nama: string } | null;
        pjgt?: { id: number; nama: string; id_pjgt?: string; no_hp?: string } | null;
    };
}>();

const confirmDelete = ref(false);
const doDelete = () => {
    router.delete(route('lembagas.destroy', props.lembaga.id), {
        onSuccess: () => confirmDelete.value = false,
    });
};

const hasCoords = props.lembaga.latitude && props.lembaga.longitude;
</script>

<template>
    <Head :title="`Detail – ${lembaga.nama}`" />
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-5xl">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('lembagas.index')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ lembaga.nama }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Detail Lembaga Penerima Guru Tugas</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('lembagas.edit', lembaga.id)"
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

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                <!-- Left: Info Cards -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Status Badge -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Keaktifan</span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold"
                                :class="lembaga.status === 'aktif'
                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                    : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                            >
                                <span
                                    class="w-2 h-2 rounded-full"
                                    :class="lembaga.status === 'aktif' ? 'bg-emerald-500' : 'bg-red-500'"
                                />
                                {{ lembaga.status === 'aktif' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Info Detail -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Informasi Lembaga</h2>

                        <!-- Alamat -->
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Alamat</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ lembaga.alamat || '—' }}
                            </p>
                        </div>

                        <!-- Wilayah -->
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Wilayah</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ lembaga.wilayah?.nama || '—' }}
                            </p>
                        </div>

                        <!-- PJGT -->
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Koordinator PJGT</p>
                            <div v-if="lembaga.pjgt">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ lembaga.pjgt.nama }}</p>
                                <p v-if="lembaga.pjgt.id_pjgt" class="text-xs text-gray-400">
                                    ID: {{ lembaga.pjgt.id_pjgt }}
                                </p>
                                <p v-if="lembaga.pjgt.no_hp" class="text-xs text-gray-400">
                                    📞 {{ lembaga.pjgt.no_hp }}
                                </p>
                            </div>
                            <p v-else class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ditentukan</p>
                        </div>
                    </div>

                    <!-- Koordinat -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-3">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Data Lokasi</h2>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Latitude</p>
                                <p class="text-sm font-mono font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ lembaga.latitude || '—' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Longitude</p>
                                <p class="text-sm font-mono font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ lembaga.longitude || '—' }}
                                </p>
                            </div>
                        </div>

                        <div v-if="lembaga.urlmap">
                            <a
                                :href="lembaga.urlmap"
                                target="_blank"
                                class="inline-flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Buka di Google Maps
                            </a>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="text-xs text-gray-400 dark:text-gray-500 px-1 space-y-1">
                        <p>📅 Ditambahkan: {{ new Date(lembaga.created_at).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }) }}</p>
                        <p>🔄 Diperbarui: {{ new Date(lembaga.updated_at).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }) }}</p>
                    </div>
                </div>

                <!-- Right: Map -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">📍 Lokasi di Peta</h2>
                            <span v-if="hasCoords" class="text-xs text-gray-400">Klik marker untuk info</span>
                            <span v-else class="text-xs text-amber-500">Koordinat belum diset</span>
                        </div>

                        <div v-if="hasCoords" class="relative">
                            <Suspense>
                                <LeafletMap
                                    mode="view"
                                    :lat="lembaga.latitude"
                                    :lng="lembaga.longitude"
                                    height="420px"
                                />
                            </Suspense>
                        </div>

                        <!-- No coords placeholder -->
                        <div v-else class="flex flex-col items-center justify-center h-[420px] text-gray-400 dark:text-gray-500 gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <p class="text-sm font-medium">Koordinat belum tersedia</p>
                            <Link
                                :href="route('lembagas.edit', lembaga.id)"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline"
                            >
                                Edit dan tambahkan koordinat →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmDelete"
            :message="`Hapus data lembaga '${lembaga.nama}'?`"
            @confirm="doDelete"
            @cancel="confirmDelete = false"
        />
    </AuthenticatedLayout>
</template>
