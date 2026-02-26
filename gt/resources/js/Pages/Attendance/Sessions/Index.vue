<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { ref, watch } from 'vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps<{
    sessions: { data: any[]; links: any[] };
    filters: any;
}>();

const columns = [
    { key: 'title', label: 'Nama Kegiatan' },
    { key: 'type', label: 'Tipe' },
    { key: 'status', label: 'Status' },
    { key: 'attendances_count', label: 'Hadir' },
    { key: 'created_at', label: 'Dibuat Pada' },
];

const search = ref(props.filters.search || '');
let searchTimeout: any = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('attendance-sessions.index'), { search: value }, {
            preserveState: true,
            replace: true
        });
    }, 500);
});

const getStatusColor = (status: string) => {
    return status === 'open' 
        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
        : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
};

const getTypeLabel = (type: string) => {
    const labels: any = {
        departure: 'Keberangkatan',
        meeting: 'Rapat',
        event: 'Kegiatan Umum',
        other: 'Lainnya'
    };
    return labels[type] || type;
};
</script>

<template>
    <Head title="Sesi Absensi" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Absensi Kegiatan</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola sesi absensi berbasis QR Code.</p>
                </div>
                <Link :href="route('attendance-sessions.create')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    Buat Sesi Baru
                </Link>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <TextInput v-model="search" placeholder="Cari kegiatan..." class="max-w-xs" />
                </div>

                <DataTable :columns="columns" :rows="sessions.data">
                    <template #title="{ row }">
                        <div>
                            <Link :href="route('attendance-sessions.show', row.id)" class="font-bold text-indigo-600 hover:text-indigo-800">
                                {{ row.title }}
                            </Link>
                            <p class="text-xs text-gray-400 truncate max-w-xs">{{ row.description }}</p>
                        </div>
                    </template>
                    <template #type="{ row }">
                        <span class="text-sm font-medium">{{ getTypeLabel(row.type) }}</span>
                    </template>
                    <template #status="{ row }">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider" :class="getStatusColor(row.status)">
                            {{ row.status === 'open' ? 'Terbuka' : 'Ditutup' }}
                        </span>
                    </template>
                    <template #attendances_count="{ row }">
                        <span class="font-bold">{{ row.attendances_count }}</span>
                        <span class="text-gray-400 text-xs ml-1">orang</span>
                    </template>
                    <template #created_at="{ row }">
                        {{ new Date(row.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                    </template>
                    <template #actions="{ row }">
                        <div class="flex items-center gap-2">
                             <Link :href="route('attendance-sessions.scanner', row.id)" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                Buka Scanner
                            </Link>
                            <Link :href="route('attendance-sessions.show', row.id)" class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                                Detail
                            </Link>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
