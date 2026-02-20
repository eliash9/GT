<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps<{
    tokens: any[];
}>();

const form = useForm({
    name: '',
});

const page = usePage() as any;

const createToken = () => {
    form.post(route('profile.tokens.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const deleteForm = useForm({});
const deleteToken = (tokenId: number) => {
    if (confirm('Are you sure you want to delete this token?')) {
        deleteForm.delete(route('profile.tokens.destroy', tokenId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Personal Access Tokens
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage your API tokens to allow third-party services to access this application on your behalf.
            </p>
        </header>

        <form @submit.prevent="createToken" class="mt-6 space-y-6">
            <div>
                <InputLabel for="token_name" value="Token Name" />
                <div class="flex items-center gap-4 mt-1">
                    <TextInput
                        id="token_name"
                        v-model="form.name"
                        type="text"
                        class="block w-full max-w-sm"
                        placeholder="e.g. Mobile App"
                        required
                    />
                    <PrimaryButton :disabled="form.processing">Create</PrimaryButton>
                </div>
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            
            <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
                <div v-if="page.props.flash?.token" class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">
                        {{ page.props.flash?.success }}
                    </p>
                    <div class="mt-2 flex items-center justify-between bg-white dark:bg-gray-900 p-2 rounded border border-emerald-100 dark:border-emerald-800/50">
                        <code class="text-emerald-600 dark:text-emerald-400 text-sm select-all">{{ page.props.flash?.token }}</code>
                    </div>
                </div>
            </Transition>
        </form>

        <div v-if="tokens && tokens.length > 0" class="mt-8">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">Active Tokens</h3>
            <ul class="space-y-3">
                <li v-for="token in tokens" :key="token.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ token.name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Created {{ new Date(token.created_at).toLocaleDateString() }}</p>
                    </div>
                    <button @click="deleteToken(token.id)" class="text-sm text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                        Delete
                    </button>
                </li>
            </ul>
        </div>
    </section>
</template>
