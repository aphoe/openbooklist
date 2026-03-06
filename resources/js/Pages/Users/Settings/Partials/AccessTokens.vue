<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/Forms/TextInput.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const page = usePage();
const tokens = computed(() => page.props.tokens || []);
const newToken = computed(() => page.props.flash?.newToken || null);

const showCreateForm = ref(false);
const confirmingRevoke = ref(null);

const form = useForm({
    name: '',
});

const createToken = () => {
    form.post(route('settings.tokens.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showCreateForm.value = false;
        },
    });
};

const revokeToken = (tokenId) => {
    router.delete(route('settings.tokens.destroy', tokenId), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingRevoke.value = null;
        },
    });
};
</script>

<template>
    <div class="space-y-8">
        <!-- New Token Flash Banner -->
        <div v-if="newToken"
            class="rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 p-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-600 dark:text-green-400 shrink-0">check_circle</span>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-green-800 dark:text-green-300 mb-1">Token Created Successfully
                    </h4>
                    <p class="text-xs text-green-700 dark:text-green-400 mb-2">Make sure to copy this token now. You
                        won't be able to see it again.</p>
                    <code
                        class="block w-full p-3 bg-white dark:bg-slate-900 border border-green-200 dark:border-green-800 rounded-lg text-sm text-slate-900 dark:text-white font-mono break-all select-all">{{ newToken }}</code>
                </div>
            </div>
        </div>

        <!-- Token Management Card -->
        <div
            class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div
                class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">API Access Tokens</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage API keys for accessing the
                        application programmatically.</p>
                </div>
                <button @click="showCreateForm = !showCreateForm"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-[20px] mr-2">add</span>
                    Create New Token
                </button>
            </div>

            <!-- Create Token Inline Form -->
            <div v-if="showCreateForm"
                class="p-6 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <form @submit.prevent="createToken" class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">Token Name</label>
                        <TextInput v-model="form.name" placeholder="e.g. Production API Key" />
                        <span v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</span>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <SubmitButton :processing="form.processing" class="!w-auto !px-6">
                            Create Token
                        </SubmitButton>
                        <button @click="showCreateForm = false; form.reset()" type="button"
                            class="px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tokens Table -->
            <div v-if="tokens.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                scope="col">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                scope="col">Last Used</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                scope="col">Abilities</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-200 dark:divide-slate-800">
                        <tr v-for="token in tokens" :key="token.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined">key</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ token.name
                                        }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">Created on {{
                                            token.created_at }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900 dark:text-white">{{ token.last_used_at || 'Never' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    {{ token.abilities.join(', ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button v-if="confirmingRevoke !== token.id" @click="confirmingRevoke = token.id"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-semibold text-xs border border-red-200 dark:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/20 px-3 py-1.5 rounded transition-colors">
                                    Revoke
                                </button>
                                <div v-else class="flex items-center justify-end gap-2">
                                    <button @click="revokeToken(token.id)"
                                        class="text-white bg-red-600 hover:bg-red-700 font-semibold text-xs px-3 py-1.5 rounded transition-colors">
                                        Confirm
                                    </button>
                                    <button @click="confirmingRevoke = null"
                                        class="text-slate-600 dark:text-slate-400 font-semibold text-xs border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 px-3 py-1.5 rounded transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else class="p-12 text-center">
                <div
                    class="size-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">key</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No Access Tokens</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6">You haven't created any API tokens yet.</p>
                <button @click="showCreateForm = true"
                    class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Create Your First Token
                </button>
            </div>

            <!-- Footer count -->
            <div v-if="tokens.length"
                class="bg-slate-50 dark:bg-slate-800 px-6 py-3 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                <span>{{ tokens.length }} active token{{ tokens.length !== 1 ? 's' : '' }}</span>
            </div>
        </div>
    </div>
</template>
