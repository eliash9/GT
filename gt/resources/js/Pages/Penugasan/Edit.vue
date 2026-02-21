<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    penugasan: any;
    lembagas:  any[];
    tahunPsms: any[];
}>();

const form = ref({
    lembaga_id:      String(props.penugasan.lembaga_id),
    tahun_psm_id:    props.penugasan.tahun_psm_id ? String(props.penugasan.tahun_psm_id) : '',
    status:          props.penugasan.status,
    catatan:         props.penugasan.catatan ?? '',
});

const formatDate = (dateString: string) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const errors  = ref<Record<string, string>>({});
const loading = ref(false);

const statuses = ['diusulkan', 'disetujui', 'aktif', 'selesai', 'dibatalkan'];
const labelStatus: Record<string, string> = {
    diusulkan: 'Diusulkan', disetujui: 'Disetujui', aktif: 'Aktif',
    selesai: 'Selesai', dibatalkan: 'Dibatalkan',
};

const submit = () => {
    loading.value = true;
    router.put(route('penugasans.update', props.penugasan.id), form.value, {
        onError: (e) => { errors.value = e; loading.value = false; },
        onSuccess: () => { loading.value = false; },
    });
};
</script>

<template>
    <Head :title="`Edit Penugasan – ${penugasan.santri?.nama}`" />
    <AuthenticatedLayout>
        <div class="max-w-2xl space-y-5">
            <div class="flex items-center gap-3">
                <Link :href="route('penugasans.show', penugasan.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Penugasan</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ penugasan.santri?.nama }} → {{ penugasan.lembaga?.nama }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">

                <!-- Santri (readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Santri</label>
                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        {{ penugasan.santri?.nama }} ({{ penugasan.santri?.nis }})
                        <span class="text-xs text-gray-400 ml-1">(tidak bisa diubah)</span>
                    </div>
                </div>

                <!-- Lembaga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Lembaga Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select v-model="form.lembaga_id"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        <option v-for="l in lembagas" :key="l.id" :value="String(l.id)">
                            {{ l.nama }} ({{ l.wilayah?.nama ?? '—' }})
                        </option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select v-model="form.status"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        <option v-for="s in statuses" :key="s" :value="s">{{ labelStatus[s] }}</option>
                    </select>
                </div>

                <!-- Tahun PSM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Masa Tugas / Tahun Ajaran</label>
                    <select v-model="form.tahun_psm_id"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        <option value="">-- Bebas (Tanpa Master) --</option>
                        <option v-for="t in tahunPsms" :key="t.id" :value="String(t.id)">
                            {{ t.judul }} 
                            ({{ formatDate(t.tanggal_mulai_masehi) }} - {{ formatDate(t.tanggal_selesai_masehi) }})
                            {{ t.aktif ? ' [Aktif]' : '' }}
                        </option>
                    </select>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catatan</label>
                    <textarea v-model="form.catatan" rows="3"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm resize-none"></textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" :disabled="loading"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg transition-colors">
                        {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                    <Link :href="route('penugasans.show', penugasan.id)" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
