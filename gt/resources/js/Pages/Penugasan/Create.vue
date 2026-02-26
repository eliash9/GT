<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    santris:   any[];
    lembagas:  any[];
    tahunPsms: any[];
}>();

const form = ref({
    santri_id:       '',
    lembaga_id:      '',
    tahun_psm_id:    '',
    tanggal_mulai:   '',
    tanggal_selesai: '',
    catatan:         '',
});

const formatDate = (dateString: string) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const errors  = ref<Record<string, string>>({});
const loading = ref(false);

const submit = () => {
    loading.value = true;
    
    // auto select active tahun psm if empty
    if (!form.value.tahun_psm_id) {
        const activeTahun = props.tahunPsms.find(t => t.aktif);
        if (activeTahun) {
            form.value.tahun_psm_id = String(activeTahun.id);
        }
    }

    router.post(route('penugasans.store'), form.value, {
        onError: (e) => { errors.value = e; loading.value = false; },
        onSuccess: () => { loading.value = false; },
    });
};
</script>

<template>
    <Head title="Tambah Penugasan" />
    <AuthenticatedLayout>
        <div class="max-w-2xl space-y-5 mx-auto">
            <div class="flex items-center gap-3">
                <Link :href="route('penugasans.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Penugasan Manual</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Atau gunakan <Link :href="route('matching.index')" class="text-indigo-500 underline">Matching Otomatis</Link> untuk rekomendasi terbaik.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">

                <!-- Santri -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Santri (Guru Tugas) <span class="text-red-500">*</span>
                    </label>
                    <select v-model="form.santri_id"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                        :class="errors.santri_id ? 'border-red-400' : ''">
                        <option value="">-- Pilih Santri --</option>
                        <option v-for="s in santris" :key="s.id" :value="String(s.id)">
                            {{ s.nama }} ({{ s.nis }}) · {{ s.kelas }} · Angk. {{ s.angkatan }}
                        </option>
                    </select>
                    <p v-if="errors.santri_id" class="text-xs text-red-500 mt-1">{{ errors.santri_id }}</p>
                    <p class="text-xs text-gray-400 mt-1">Hanya santri dengan status "Menunggu" yang ditampilkan.</p>
                </div>

                <!-- Lembaga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Lembaga Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select v-model="form.lembaga_id"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                        :class="errors.lembaga_id ? 'border-red-400' : ''">
                        <option value="">-- Pilih Lembaga --</option>
                        <option v-for="l in lembagas" :key="l.id" :value="String(l.id)">
                            {{ l.nama }} ({{ l.wilayah?.nama ?? '—' }})
                        </option>
                    </select>
                    <p v-if="errors.lembaga_id" class="text-xs text-red-500 mt-1">{{ errors.lembaga_id }}</p>
                </div>

                <!-- Tahun PSM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Masa Tugas / Tahun Ajaran</label>
                    <select v-model="form.tahun_psm_id"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                        :class="errors.tahun_psm_id ? 'border-red-400' : ''">
                        <option value="">-- Pilih Periode Penugasan --</option>
                        <option v-for="t in tahunPsms" :key="t.id" :value="String(t.id)">
                            {{ t.judul }} 
                            ({{ formatDate(t.tanggal_mulai_masehi) }} - {{ formatDate(t.tanggal_selesai_masehi) }})
                            {{ t.aktif ? ' [Aktif]' : '' }}
                        </option>
                    </select>
                    <p v-if="errors.tahun_psm_id" class="text-xs text-red-500 mt-1">{{ errors.tahun_psm_id }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai (Opsional)</label>
                        <input type="date" v-model="form.tanggal_mulai"
                            class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                            :class="errors.tanggal_mulai ? 'border-red-400' : ''" />
                        <p v-if="errors.tanggal_mulai" class="text-xs text-red-500 mt-1">{{ errors.tanggal_mulai }}</p>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai (Opsional)</label>
                        <input type="date" v-model="form.tanggal_selesai"
                            class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                            :class="errors.tanggal_selesai ? 'border-red-400' : ''" />
                        <p v-if="errors.tanggal_selesai" class="text-xs text-red-500 mt-1">{{ errors.tanggal_selesai }}</p>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catatan</label>
                    <textarea v-model="form.catatan" rows="3"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm resize-none"
                        placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" :disabled="loading"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg transition-colors">
                        {{ loading ? 'Menyimpan...' : 'Simpan Penugasan' }}
                    </button>
                    <Link :href="route('penugasans.index')" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
