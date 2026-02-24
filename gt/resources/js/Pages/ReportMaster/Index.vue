<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps<{
    categories: any[];
    currentType: string;
}>();

// Navigation Types
const switchType = (type: string) => {
    router.get(route('report-categories.index', { type }));
};

// CATEGORY MODAL
const showCategoryModal = ref(false);
const editingCategory = ref<any>(null);

const categoryForm = useForm({
    name: '',
    report_type: props.currentType,
    order: 0,
});

const openCategoryModal = (category: any = null) => {
    editingCategory.value = category;
    if (category) {
        categoryForm.name = category.name;
        categoryForm.report_type = category.report_type;
        categoryForm.order = category.order;
    } else {
        categoryForm.reset();
        categoryForm.report_type = props.currentType;
    }
    showCategoryModal.value = true;
};

const submitCategory = () => {
    if (editingCategory.value) {
        categoryForm.put(route('report-categories.update', editingCategory.value.id), {
            onSuccess: () => showCategoryModal.value = false,
        });
    } else {
        categoryForm.post(route('report-categories.store'), {
            onSuccess: () => showCategoryModal.value = false,
        });
    }
};

const deleteCategory = (category: any) => {
    if (confirm(`Yakin ingin menghapus kategori "${category.name}"?`)) {
        router.delete(route('report-categories.destroy', category.id));
    }
};

// QUESTION MODAL
const showQuestionModal = ref(false);
const editingQuestion = ref<any>(null);

const questionForm = useForm({
    report_category_id: '' as string|number,
    question: '',
    type: 'text',
    optionsText: '', // Helper field since form needs array
    is_required: true,
    order: 0,
});

const openQuestionModal = (categoryId: string|number, question: any = null) => {
    editingQuestion.value = question;
    if (question) {
        questionForm.report_category_id = question.report_category_id;
        questionForm.question = question.question;
        questionForm.type = question.type;
        questionForm.optionsText = question.options ? question.options.join(',') : '';
        questionForm.is_required = question.is_required;
        questionForm.order = question.order;
    } else {
        questionForm.reset();
        questionForm.report_category_id = categoryId;
    }
    showQuestionModal.value = true;
};

const submitQuestion = () => {
    // Convert comma separated string to array for backend
    const optionsArray = questionForm.optionsText
        ? questionForm.optionsText.split(',').map(opt => opt.trim()).filter(opt => opt)
        : null;

    const payload = {
        ...questionForm.data(),
        options: optionsArray,
    };

    if (editingQuestion.value) {
        router.put(route('report-questions.update', editingQuestion.value.id), payload, {
            onSuccess: () => showQuestionModal.value = false,
            preserveScroll: true
        });
    } else {
        router.post(route('report-questions.store'), payload, {
            onSuccess: () => showQuestionModal.value = false,
            preserveScroll: true
        });
    }
};

const deleteQuestion = (question: any) => {
    if (confirm(`Yakin ingin menghapus pertanyaan "${question.question}"?`)) {
        router.delete(route('report-questions.destroy', question.id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Master Laporan" />
    <AuthenticatedLayout>
        <div class="space-y-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header section & Buttons -->
            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Master Pertanyaan Laporan</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola master pertanyaan dan kategori untuk laporan dinamis.</p>
                </div>
            </div>

            <!-- TABS -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button @click="switchType('gt')"
                            :class="currentType === 'gt' ? 'border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Laporan Guru Tugas
                    </button>
                    <button @click="switchType('pjgt')"
                            :class="currentType === 'pjgt' ? 'border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Laporan PJGT
                    </button>
                    <button @click="switchType('korwil')"
                            :class="currentType === 'korwil' ? 'border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Laporan Korwil
                    </button>
                </nav>
            </div>

            <div class="flex justify-end">
                 <PrimaryButton @click="openCategoryModal()">
                     + Tambah Kategori
                 </PrimaryButton>
            </div>

            <!-- Kategori / Pertanyaan List -->
            <div v-for="cat in categories" :key="cat.id" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 mb-6 !mt-4">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Urutan: {{ cat.order }}</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ cat.name }}</h3>
                    </div>
                    <div class="flex space-x-2">
                        <SecondaryButton @click="openCategoryModal(cat)" class="!py-1 !px-2 text-xs">Edit</SecondaryButton>
                        <DangerButton @click="deleteCategory(cat)" class="!py-1 !px-2 text-xs">Hapus</DangerButton>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="cat.questions.length === 0" class="text-center py-4 text-gray-500 text-sm">
                        Belum ada pertanyaan di kategori ini.
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="(q, idx) in cat.questions" :key="q.id" class="flex items-start justify-between border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                           <div>
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-xs bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded px-2 py-0.5">No. {{ String(Number(idx) + 1).padStart(2, '0') }}</span>
                                    <span v-if="q.is_required" class="text-xs text-red-500 font-semibold">* Wajib</span>
                                </div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ q.question }}</p>
                                <div class="mt-2 text-xs text-gray-500">
                                    Tipe: <span class="font-semibold uppercase">{{ q.type }}</span>
                                    <span v-if="['radio', 'select', 'checkbox'].includes(q.type) && q.options" class="ml-2">
                                        Opsi: {{ q.options.join(', ') }}
                                    </span>
                                </div>
                           </div>
                           <div class="flex space-x-2 ml-4 flex-shrink-0">
                                <SecondaryButton @click="openQuestionModal(cat.id, q)" class="!py-1 !px-2 text-xs">Edit</SecondaryButton>
                                <DangerButton @click="deleteQuestion(q)" class="!py-1 !px-2 text-xs">Hapus</DangerButton>
                           </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                         <button @click="openQuestionModal(cat.id)" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                             + Tambah Pertanyaan
                         </button>
                    </div>
                </div>
            </div>
            
            <div v-if="categories.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                 <p class="text-gray-500">Belum ada kategori laporan untuk tipe ini.</p>
                 <PrimaryButton @click="openCategoryModal()" class="mt-4">+ Buat Kategori Pertama</PrimaryButton>
            </div>
        </div>

        <!-- Modal Category -->
        <Modal :show="showCategoryModal" @close="showCategoryModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ editingCategory ? 'Edit Kategori' : 'Tambah Kategori' }}
                </h2>
                <div class="space-y-4">
                    <div>
                        <InputLabel value="Nama Kategori" />
                        <TextInput v-model="categoryForm.name" class="mt-1 block w-full" placeholder="Cth: Kegiatan Mengajar" />
                        <InputError :message="categoryForm.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="No. Urut (opsional)" />
                        <input type="number" v-model="categoryForm.order" class="mt-1 block w-1/3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" />
                        <InputError :message="categoryForm.errors.order" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showCategoryModal = false" class="mr-3">Batal</SecondaryButton>
                    <PrimaryButton @click="submitCategory" :disabled="categoryForm.processing">Simpan</PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal Question -->
        <Modal :show="showQuestionModal" @close="showQuestionModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ editingQuestion ? 'Edit Pertanyaan' : 'Tambah Pertanyaan' }}
                </h2>
                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    <div>
                        <InputLabel value="Pertanyaan" />
                        <textarea v-model="questionForm.question" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <InputLabel value="Tipe Inputan" />
                        <select v-model="questionForm.type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                            <option value="text">Teks Pendek (1 Baris)</option>
                            <option value="textarea">Teks Panjang (Paragraf)</option>
                            <option value="radio">Pilihan Ganda (Hanya 1 Opsi - Radio)</option>
                            <option value="checkbox">Pilihan Ganda (Bisa Banyak Opsi - Checkbox)</option>
                            <option value="select">Dropdown Select (Hanya 1 Opsi)</option>
                        </select>
                    </div>
                    
                    <div v-if="['radio', 'select', 'checkbox'].includes(questionForm.type)">
                         <InputLabel value="Pilihan Jawaban (Pisahkan dengan Koma)" />
                         <TextInput v-model="questionForm.optionsText" class="mt-1 block w-full" placeholder="Sangat Baik, Baik, Cukup, Kurang" />
                         <p class="text-xs text-gray-500 mt-1">Cth: Sangat Paham,Cukup Paham,Kurang Paham</p>
                    </div>
                    
                    <div class="flex items-center space-x-2 mt-4">
                        <input type="checkbox" v-model="questionForm.is_required" id="isreq" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                        <label for="isreq" class="text-sm font-medium text-gray-700 dark:text-gray-300">Wajib Diisi (Required)</label>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t flex justify-end">
                    <SecondaryButton @click="showQuestionModal = false" class="mr-3">Batal</SecondaryButton>
                    <PrimaryButton @click="submitQuestion">Simpan</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
