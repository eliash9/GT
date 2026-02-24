<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import TextInput from '@/Components/TextInput.vue';
import { usePermission } from '@/composables/usePermission';

const props = defineProps<{
    reports: { data: any[]; links: any[] };
    filters?: any;
    summary?: any;
}>();

const { can } = usePermission();

const columns = [
    { key: 'report_type', label: 'Tipe' },
    { key: 'reporter_name', label: 'Nama Pelapor' }, 
    { key: 'contexts', label: 'Info Penugasan' }, 
    { key: 'period', label: 'Periode' },
    { key: 'status', label: 'Status' },
];

const checkAndEdit = (row: any) => {
    router.visit(route('reports.edit', row.id));
};

// Filter State
const search = ref(props.filters?.search || '');
const filterType = ref(props.filters?.type || '');
const filterStatus = ref(props.filters?.status || '');
const filterMonth = ref(props.filters?.month || '');
const filterYear = ref(props.filters?.year || '');

let searchTimeout: any = null;

const applyFilters = () => {
    router.get(route('reports.index'), {
        search: search.value,
        type: filterType.value,
        status: filterStatus.value,
        month: filterMonth.value,
        year: filterYear.value,
    }, { preserveState: true, replace: true });
};

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 500);
});

watch([filterType, filterStatus, filterMonth, filterYear], () => {
    applyFilters();
});

const handleExport = (e: Event) => {
    if (!filterType.value) {
        e.preventDefault();
        alert('Silakan pilih salah satu Filter Tipe (GT/PJGT/Korwil) terlebih dahulu untuk mengunduh Excel!');
    }
};

const months = [
     { value: 1, label: "Syawal - Dzulqo'dah" },
        { value: 2, label: "Dzulqo'dah - Dzulhijjah" },
        { value: 3, label: "Dzulhijjah - Muhram" },
        { value: 4, label: "Muharam - Safar" },
        { value: 5, label: "Safar - Rabi'ul Awal" },
        { value: 6, label: "Rabi'ul Awal - Rabi'ul Akhir" },
        { value: 7, label: "Rabi'ul Akhir - Jumadil Awal" },
        { value: 8, label: "Jumadil Awal - Jumadil Akhir" },
        { value: 9, label: "Jumadil Akhir - Rajab" },
        { value: 10, label: "Rajab - Sya'ban" },
];

const currentHijri = Math.floor((new Date().getFullYear() - 622) * 33 / 32);
const years = Array.from({length: 5}, (_, i) => currentHijri - 2 + i);

const getPeriodLabel = (val: number) => {
    const found = months.find(m => m.value === val);
    return found ? found.label : (val || '-');
};

</script>

<template>
    <Head title="Daftar Laporan" />
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Summary Cards -->
            <div class="grid grid-cols-4 gap-4">
                 <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                      <p class="text-sm font-medium text-gray-500">Total Semua Laporan</p>
                      <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ summary?.total || 0 }}</h3>
                 </div>
                 <div class="bg-yellow-50 p-4 rounded-lg shadow border border-yellow-200">
                      <p class="text-sm font-medium text-yellow-700">Dalam Draft / Proses</p>
                      <h3 class="text-2xl font-bold text-yellow-900">{{ summary?.draft || 0 }}</h3>
                 </div>
                 <div class="bg-green-50 p-4 rounded-lg shadow border border-green-200">
                      <p class="text-sm font-medium text-green-700">Selesai (Terkirim)</p>
                      <h3 class="text-2xl font-bold text-green-900">{{ summary?.submitted || 0 }}</h3>
                 </div>
                 <div class="bg-blue-50 p-4 rounded-lg shadow border border-blue-200">
                      <p class="text-sm font-medium text-blue-700">Telah Dievaluasi</p>
                      <h3 class="text-2xl font-bold text-blue-900">{{ summary?.evaluated || 0 }}</h3>
                 </div>
            </div>

            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Daftar Laporan</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data laporan yang diisi oleh guru tugas, pjgt, maupun manual oleh admin.</p>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700 flex flex-wrap gap-4 items-center">
                 <div class="flex-1 min-w-[200px]">
                      <TextInput v-model="search" placeholder="Cari nama pelapor atau wilayah..." class="w-full text-sm" />
                 </div>
                 <select v-model="filterType" class="rounded-md border-gray-300 text-sm shadow-sm">
                      <option value="">Semua Tipe</option>
                      <option value="gt">GT</option>
                      <option value="pjgt">PJGT</option>
                      <option value="korwil">Korwil</option>
                 </select>
                 <select v-model="filterStatus" class="rounded-md border-gray-300 text-sm shadow-sm">
                      <option value="">Semua Status</option>
                      <option value="draft">Draft</option>
                      <option value="submitted">Terkirim</option>
                      <option value="evaluated">Dievaluasi</option>
                 </select>
                 <select v-model="filterMonth" class="rounded-md border-gray-300 text-sm shadow-sm">
                      <option value="">Bulan</option>
                      <option v-for="m in months" :value="m.value">{{ m.label }}</option>
                 </select>
                 <select v-model="filterYear" class="rounded-md border-gray-300 text-sm shadow-sm">
                      <option value="">Tahun</option>
                      <option v-for="y in years" :value="y">{{ y }}</option>
                 </select>
                 
                 <div>
                      <a :href="filterType ? route('reports.index', { export: 'excel', type: filterType, status: filterStatus, month: filterMonth, year: filterYear }) : '#'"
                         @click="handleExport"
                         class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                          Unduh Rekap Excel
                      </a>
                 </div>
            </div>

            <DataTable
                :columns="columns"
                :rows="reports.data"
                create-route="reports.create"
                create-label="Input Laporan Manual"
                :can-create="true"
            >
                <template #period="{ row }">
                     {{ getPeriodLabel(row.period_month) }} <br><span class="text-xs text-gray-500 font-semibold">{{ row.period_year }} H</span>
                </template>
                <template #contexts="{ row }">
                     <div class="text-[11px] leading-tight text-gray-600 dark:text-gray-400">
                          <span v-if="row.lembaga_id">🏬 {{ row.lembaga?.nama || 'N/A' }}<br></span>
                          <span v-if="row.pjgt_id && row.report_type!=='pjgt'">👤 PJGT: {{ row.pjgt?.nama || 'N/A' }}<br></span>
                          <span v-if="row.santri_id && row.report_type!=='gt'">🎓 GT: {{ row.santri?.nama || 'N/A' }}</span>
                     </div>
                </template>
                <template #report_type="{ row }">
                    <span class="uppercase font-bold text-xs">{{ row.report_type }}</span>
                </template>
                <template #status="{ row }">
                     <span class="px-2 py-1 text-xs rounded uppercase font-semibold"
                           :class="{
                               'bg-yellow-100 text-yellow-800': row.status === 'draft',
                               'bg-green-100 text-green-800': row.status === 'submitted',
                               'bg-blue-100 text-blue-800': row.status === 'evaluated',
                           }">
                           {{ row.status === 'submitted' ? 'Terkirim' : (row.status === 'evaluated' ? 'Dievaluasi' : 'Draft') }}
                     </span>
                </template>
                <template #actions="{ row }">
                     <button @click="checkAndEdit(row)" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                         Buka Laporan
                     </button>
                </template>
            </DataTable>
        </div>
    </AuthenticatedLayout>
</template>
