<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    skill: any;
    kategoris: string[];
}>();

const form = ref({
    nama:      props.skill.nama,
    kategori:  props.skill.kategori,
    deskripsi: props.skill.deskripsi ?? '',
    aktif:     props.skill.aktif,
});

const errors  = ref<Record<string, string>>({});
const loading = ref(false);

const labelKategori: Record<string, string> = {
    ilmu: 'Ilmu Agama', hafalan: 'Hafalan', seni: 'Seni',
    organisasi: 'Organisasi', teknologi: 'Teknologi', lainnya: 'Lainnya',
};

const submit = () => {
    loading.value = true;
    router.put(route('skills.update', props.skill.id), form.value, {
        onError: (e) => { errors.value = e; loading.value = false; },
        onSuccess: () => { loading.value = false; },
    });
};
</script>

<template>
    <Head :title="`Edit Skill – ${skill.nama}`" />
    <AuthenticatedLayout>
        <div class="max-w-lg space-y-5">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Link :href="route('skills.show', skill.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Skill</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ skill.nama }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nama Skill <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.nama" type="text"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                        :class="errors.nama ? 'border-red-400' : ''" />
                    <p v-if="errors.nama" class="text-xs text-red-500 mt-1">{{ errors.nama }}</p>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        <button v-for="k in kategoris" :key="k" type="button"
                            @click="form.kategori = k"
                            class="py-2 px-3 rounded-lg border text-sm font-medium transition-colors"
                            :class="form.kategori === k
                                ? 'bg-indigo-600 border-indigo-600 text-white'
                                : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            {{ labelKategori[k] }}
                        </button>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi</label>
                    <textarea v-model="form.deskripsi" rows="3"
                        class="input w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm resize-none"></textarea>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center gap-3">
                    <button type="button" @click="form.aktif = !form.aktif"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200"
                        :class="form.aktif ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600'">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200"
                            :class="form.aktif ? 'translate-x-5' : 'translate-x-0'" />
                    </button>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ form.aktif ? 'Skill Aktif' : 'Skill Non-Aktif' }}</span>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" :disabled="loading"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg transition-colors">
                        {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                    <Link :href="route('skills.show', skill.id)" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Batal</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
