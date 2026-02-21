<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    skills: { data: any[]; links: any[] };
    filters?: { search?: string; kategori?: string };
    kategoris: string[];
}>();

const search   = ref(props.filters?.search ?? '');
const kategori = ref(props.filters?.kategori ?? '');

const doFilter = debounce(() => {
    router.get(route('skills.index'), { search: search.value, kategori: kategori.value }, {
        preserveState: true, replace: true,
    });
}, 300);

watch([search, kategori], doFilter);

const confirmDelete = ref<any>(null);
const doDelete = () => {
    router.delete(route('skills.destroy', confirmDelete.value.id), {
        onSuccess: () => (confirmDelete.value = null),
    });
};

const labelKategori: Record<string, string> = {
    ilmu: 'Ilmu Agama', hafalan: 'Hafalan', seni: 'Seni',
    organisasi: 'Organisasi', teknologi: 'Teknologi', lainnya: 'Lainnya',
};

const badgeKategori: Record<string, string> = {
    ilmu:        'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    hafalan:     'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    seni:        'bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
    organisasi:  'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    teknologi:   'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    lainnya:     'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
};
</script>

<template>
    <Head title="Master Skill" />
    <AuthenticatedLayout>
        <div class="space-y-5">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Master Skill</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola daftar keahlian baku untuk guru tugas dan lembaga.</p>
                </div>
                <Link :href="route('skills.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Skill
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="search" type="text" placeholder="Cari nama skill..."
                        class="pl-10 input block w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm" />
                </div>
                <select v-model="kategori"
                    class="input rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm">
                    <option value="">Semua Kategori</option>
                    <option v-for="k in kategoris" :key="k" :value="k">{{ labelKategori[k] }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left">Nama Skill</th>
                                <th class="px-5 py-3 text-left">Kategori</th>
                                <th class="px-5 py-3 text-center">Santri</th>
                                <th class="px-5 py-3 text-center">Lembaga</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="skills.data.length === 0">
                                <td colspan="6" class="text-center py-12 text-gray-400 dark:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    Belum ada data skill.
                                </td>
                            </tr>
                            <tr v-for="skill in skills.data" :key="skill.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3">
                                    <Link :href="route('skills.show', skill.id)"
                                        class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ skill.nama }}
                                    </Link>
                                    <p v-if="skill.deskripsi" class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ skill.deskripsi }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full" :class="badgeKategori[skill.kategori]">
                                        {{ labelKategori[skill.kategori] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ skill.santris_count ?? 0 }}</span>
                                    <span class="text-xs text-gray-400 ml-1">santri</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ skill.lembaga_kebutuhans_count ?? 0 }}</span>
                                    <span class="text-xs text-gray-400 ml-1">lembaga</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium"
                                        :class="skill.aktif ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400'">
                                        {{ skill.aktif ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('skills.edit', skill.id)"
                                            class="text-xs text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 font-medium">Edit</Link>
                                        <button @click="confirmDelete = skill"
                                            class="text-xs text-red-400 hover:text-red-600 font-medium">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex gap-1 justify-end" v-if="skills.links && skills.links.length > 3">
                <component v-for="link in skills.links" :key="link.label"
                    :is="link.url ? Link : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border dark:border-gray-700"
                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : link.url ? 'border-gray-300 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' : 'border-gray-200 text-gray-400 cursor-not-allowed'" />
            </div>
        </div>

        <ConfirmModal
            :show="!!confirmDelete"
            :message="`Hapus skill '${confirmDelete?.nama}'? Ini akan menghapus relasi ke semua santri dan lembaga.`"
            @confirm="doDelete"
            @cancel="confirmDelete = null"
        />
    </AuthenticatedLayout>
</template>
