<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    settings: Record<string, any[]>;
}>();

// Flatten for form submission
const allSettings = Object.values(props.settings).flat();

const form = useForm({
    settings: allSettings.map((s: any) => ({
        key:   s.key,
        value: s.type === 'boolean' ? !!Number(s.value) : s.value,
    })),
});

const submit = () => form.post(route('settings.update'));

const getFormItem = (key: string) => form.settings.find((s: any) => s.key === key)!;

const handleFileChange = (key: string, e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        getFormItem(key).value = target.files[0];
    }
};

const groupLabels: Record<string, string> = {
    general: 'General Settings',
    email:   'Email Settings',
};
</script>

<template>
    <Head title="Settings" />
    <AuthenticatedLayout>
        <div class="max-w-3xl space-y-6 mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure your application settings.</p>
            </div>

            <!-- Flash success -->
            <div v-if="$page.props.flash?.success" class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div
                    v-for="(groupSettings, groupKey) in settings"
                    :key="groupKey"
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden"
                >
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ groupLabels[String(groupKey)] ?? String(groupKey) }}</h2>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        <div v-for="setting in groupSettings" :key="setting.key" class="flex items-start justify-between gap-4 px-6 py-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ setting.label }}</label>
                                <p v-if="setting.description" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ setting.description }}</p>
                            </div>
                            <div class="shrink-0 w-64">
                                <!-- Boolean toggle -->
                                <label v-if="setting.type === 'boolean'" class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="getFormItem(setting.key).value" class="sr-only peer" />
                                    <div class="w-11 h-6 bg-gray-200 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                </label>
                                <!-- Integer input -->
                                <input v-else-if="setting.type === 'integer'" type="number" v-model="getFormItem(setting.key).value" class="input dark:bg-gray-900 dark:border-gray-600 dark:text-white" />
                                <!-- Image input -->
                                <div v-else-if="setting.type === 'image'">
                                    <div v-if="typeof setting.value === 'string' && setting.value" class="mb-2">
                                        <img :src="setting.value" class="h-12 w-auto object-contain bg-gray-100 dark:bg-gray-900 p-1 rounded" />
                                    </div>
                                    <input type="file" @change="handleFileChange(setting.key, $event)" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-white" />
                                </div>
                                <!-- String input -->
                                <input v-else type="text" v-model="getFormItem(setting.key).value" class="input dark:bg-gray-900 dark:border-gray-600 dark:text-white" />
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="btn-primary">
                    {{ form.processing ? 'Saving…' : 'Save Settings' }}
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
