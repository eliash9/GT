<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps<{
    lembagas:        any[];
    selectedLembaga: any | null;
    rekomendasi:     any[];
    wilayahs:        any[];
    filters?:        { lembaga_id?: string; wilayah_id?: string };
}>();

const wilayahId  = ref(props.filters?.wilayah_id ?? '');
const lembagaId  = ref(props.filters?.lembaga_id ?? '');
const assigning  = ref<number | null>(null);

const lembagasTerfilter = computed(() => {
    if (!wilayahId.value) return props.lembagas;
    return props.lembagas.filter(l => String(l.wilayah_id) === String(wilayahId.value));
});

watch(wilayahId, () => {
    lembagaId.value = '';
    router.get(route('matching.index'), { wilayah_id: wilayahId.value }, {
        preserveState: true, replace: true,
    });
});

const pilihLembaga = (id: number) => {
    lembagaId.value = String(id);
    router.get(route('matching.index'), {
        lembaga_id: id,
        wilayah_id: wilayahId.value,
    }, { preserveState: true, replace: true });
};

const assignSantri = (santriId: number, skor: number) => {
    assigning.value = santriId;
    router.post(route('matching.assign'), {
        santri_id:  santriId,
        lembaga_id: props.selectedLembaga.id,
        skor,
    }, {
        onFinish: () => { assigning.value = null; },
    });
};

const badgePrioritas: Record<string, string> = {
    wajib:      'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400',
    diutamakan: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    opsional:   'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
};
</script>

<template>
    <Head title="Matching Guru Tugas" />
    <AuthenticatedLayout>
        <div class="space-y-5">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🎯 Matching Guru Tugas</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Pilih lembaga untuk melihat rekomendasi santri yang paling cocok berdasarkan kebutuhan skill.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- Kolom Kiri: Pilih Lembaga -->
                <div class="space-y-4">
                    <!-- Filter Wilayah -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                            Filter Wilayah
                        </label>
                        <select v-model="wilayahId"
                            class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                            <option value="">Semua Wilayah</option>
                            <option v-for="w in wilayahs" :key="w.id" :value="String(w.id)">{{ w.nama }}</option>
                        </select>
                    </div>

                    <!-- Daftar Lembaga -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Pilih Lembaga ({{ lembagasTerfilter.length }})
                            </p>
                        </div>
                        <div class="divide-y divide-gray-50 dark:divide-gray-700 max-h-[60vh] overflow-y-auto">
                            <button v-for="l in lembagasTerfilter" :key="l.id"
                                @click="pilihLembaga(l.id)"
                                class="w-full text-left px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors"
                                :class="selectedLembaga?.id === l.id ? 'bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-indigo-500' : ''">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ l.nama }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-400">{{ l.wilayah?.nama ?? '—' }}</span>
                                    <span class="text-gray-300 dark:text-gray-600">·</span>
                                    <span class="text-xs"
                                        :class="(l.kebutuhans?.length ?? 0) > 0 ? 'text-indigo-500' : 'text-gray-400'">
                                        {{ l.kebutuhans?.length ?? 0 }} kebutuhan skill
                                    </span>
                                    <span class="text-gray-300 dark:text-gray-600">·</span>
                                    <span class="text-xs"
                                        :class="(l.penugasan_aktif_count ?? 0) > 0 ? 'text-emerald-500' : 'text-gray-400'">
                                        {{ l.penugasan_aktif_count ?? 0 }} guru aktif
                                    </span>
                                </div>
                            </button>
                            <div v-if="lembagasTerfilter.length === 0" class="py-8 text-center text-sm text-gray-400">
                                Tidak ada lembaga di wilayah ini.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail + Rekomendasi -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Placeholder belum pilih -->
                    <div v-if="!selectedLembaga"
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center justify-center py-20 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-200 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Pilih lembaga di sebelah kiri</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">untuk melihat rekomendasi santri yang cocok</p>
                    </div>

                    <template v-else>
                        <!-- Info Lembaga Terpilih -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ selectedLembaga.nama }}</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedLembaga.wilayah?.nama ?? '—' }}</p>
                                </div>
                                <Link :href="route('lembagas.show', selectedLembaga.id)"
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline whitespace-nowrap">Lihat Detail →</Link>
                            </div>

                            <!-- Kebutuhan Skill Lembaga -->
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                                    Kebutuhan Skill Lembaga
                                </p>
                                <div v-if="selectedLembaga.kebutuhans?.length > 0" class="flex flex-wrap gap-2">
                                    <div v-for="k in selectedLembaga.kebutuhans" :key="k.id"
                                        class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs border"
                                        :class="badgePrioritas[k.prioritas]">
                                        <span class="font-medium">{{ k.skill?.nama }}</span>
                                        <span class="opacity-70">· {{ k.prioritas }} · kuota {{ k.kuota }}</span>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-3 py-2 rounded-lg">
                                    ⚠️ Lembaga ini belum mengisi kebutuhan skill.
                                    <Link :href="route('lembagas.edit', selectedLembaga.id)" class="underline ml-1">Tambah sekarang</Link>
                                </div>
                            </div>
                        </div>

                        <!-- Hasil Rekomendasi -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Rekomendasi Santri</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Urut dari yang paling cocok</p>
                                </div>
                                <span class="text-xs bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-full font-medium">
                                    {{ rekomendasi.length }} kandidat
                                </span>
                            </div>

                            <div v-if="rekomendasi.length === 0"
                                class="py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Tidak ada santri yang cocok atau semua sudah bertugas.
                            </div>

                            <div v-else class="divide-y divide-gray-50 dark:divide-gray-700">
                                <div v-for="(rec, idx) in rekomendasi" :key="rec.santri.id"
                                    class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <div class="flex items-start gap-4">
                                        <!-- Rank -->
                                        <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold"
                                            :class="idx === 0 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                                                  : idx === 1 ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
                                                  : idx === 2 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
                                                  : 'bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-400'">
                                            {{ idx + 1 }}
                                        </div>

                                        <!-- Info Santri -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <Link :href="route('santris.show', rec.santri.id)"
                                                    class="font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                                    {{ rec.santri.nama }}
                                                </Link>
                                                <span class="text-xs text-gray-400">{{ rec.santri.nis }}</span>
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                                    {{ rec.santri.jenis_kelamin === 'L' ? '♂' : '♀' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ rec.santri.kelas }} · Angkatan {{ rec.santri.angkatan }}</p>

                                            <!-- Progress bar skor -->
                                            <div class="mt-2">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Skor Kecocokan</span>
                                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ rec.skor }} poin ({{ rec.pct }}%)</span>
                                                </div>
                                                <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                    <div class="h-2 rounded-full transition-all duration-500"
                                                        :class="rec.pct >= 80 ? 'bg-emerald-500' : rec.pct >= 50 ? 'bg-indigo-500' : 'bg-yellow-400'"
                                                        :style="`width: ${rec.pct}%`" />
                                                </div>
                                            </div>

                                            <!-- Skill terpenuhi & belum -->
                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                <span v-for="s in rec.terpenuhi" :key="s"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                                                    ✓ {{ s }}
                                                </span>
                                                <span v-for="s in rec.belum_terpenuhi" :key="s.nama"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full"
                                                    :class="s.prioritas === 'wajib' ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'">
                                                    ✗ {{ s.nama }}
                                                    <span v-if="s.prioritas === 'wajib'" class="font-bold text-red-500">!</span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Tombol Assign -->
                                        <div class="flex-shrink-0">
                                            <button
                                                @click="assignSantri(rec.santri.id, rec.skor)"
                                                :disabled="assigning === rec.santri.id"
                                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap">
                                                {{ assigning === rec.santri.id ? '...' : 'Tugaskan' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
