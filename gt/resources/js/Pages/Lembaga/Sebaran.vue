<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { defineAsyncComponent, computed, ref } from 'vue';

const LeafletMap = defineAsyncComponent(() => import('@/Components/LeafletMap.vue'));

const props = defineProps<{
    lembagas: Array<{
        id: number;
        nama: string;
        alamat: string | null;
        latitude: string | null;
        longitude: string | null;
        urlmap: string | null;
        status: string;
        wilayah?: { nama: string } | null;
        pjgt?: { nama: string } | null;
    }>;
}>();

const filterStatus = ref<'semua' | 'aktif' | 'non-aktif'>('semua');

const filtered = computed(() => {
    return props.lembagas.filter(l => {
        if (filterStatus.value !== 'semua' && l.status !== filterStatus.value) return false;
        return true;
    });
});

const withCoords = computed(() => filtered.value.filter(l => l.latitude && l.longitude));
const withoutCoords = computed(() => filtered.value.filter(l => !l.latitude || !l.longitude));

const mapMarkers = computed(() => withCoords.value.map(l => ({
    lat: l.latitude!,
    lng: l.longitude!,
    title: l.nama,
    subtitle: [l.wilayah?.nama, l.pjgt?.nama].filter(Boolean).join(' · '),
    status: l.status,
    url: route('lembagas.show', l.id),
})));
</script>

<template>
    <Head title="Sebaran Peta Lembaga" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🗺️ Sebaran Peta Lembaga</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Visualisasi lokasi seluruh lembaga penerima Guru Tugas.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('lembagas.index')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Tabel
                    </Link>
                    <Link :href="route('lembagas.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Lembaga
                    </Link>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ lembagas.length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total Lembaga</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ lembagas.filter(l => l.status === 'aktif').length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Aktif</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ lembagas.filter(l => l.latitude && l.longitude).length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Punya Koordinat</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-amber-500">{{ lembagas.filter(l => !l.latitude || !l.longitude).length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Belum Ada Koordinat</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Filter:</span>
                <button
                    v-for="opt in [{ value: 'semua', label: 'Semua' }, { value: 'aktif', label: 'Aktif' }, { value: 'non-aktif', label: 'Non-Aktif' }]"
                    :key="opt.value"
                    @click="filterStatus = opt.value as any"
                    class="px-3 py-1 text-xs font-medium rounded-full border transition-colors"
                    :class="filterStatus === opt.value
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-indigo-400'"
                >
                    {{ opt.label }}
                </button>
                <span class="text-xs text-gray-400 ml-2">
                    Menampilkan {{ withCoords.length }} titik di peta
                </span>
            </div>

            <!-- Map -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Klik marker pada peta untuk info lembaga
                    </p>
                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1">
                            <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Semua lembaga
                        </span>
                    </div>
                </div>

                <Suspense>
                    <LeafletMap
                        mode="sebaran"
                        :markers="mapMarkers"
                        height="520px"
                    />
                    <template #fallback>
                        <div class="flex items-center justify-center h-[520px] text-gray-400 text-sm">
                            Memuat peta…
                        </div>
                    </template>
                </Suspense>
            </div>

            <!-- Lembaga tanpa koordinat -->
            <div v-if="withoutCoords.length > 0">
                <div class="flex items-center gap-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ withoutCoords.length }} lembaga belum memiliki koordinat
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div
                        v-for="l in withoutCoords"
                        :key="l.id"
                        class="bg-white dark:bg-gray-800 rounded-xl border border-amber-200 dark:border-amber-800/50 p-4 flex items-start justify-between gap-2"
                    >
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ l.nama }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ l.wilayah?.nama || 'Wilayah tidak diset' }}</p>
                        </div>
                        <Link
                            :href="route('lembagas.edit', l.id)"
                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline whitespace-nowrap font-medium shrink-0"
                        >
                            Set Lokasi →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
