<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SearchableDropdown from '@/Components/Forms/SearchableDropdown.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const hasOpenRouterKey = computed(() => page.props.hasOpenRouterKey);
const aiModels = computed(() => page.props.aiModels || []);

const form = useForm({
    use_ai_description: user.value?.use_ai_description ?? false,
    ai_model: user.value?.ai_model ?? null,
});

const submit = () => {
    form.put(route('settings.ai'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <div
            class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 min-h-[600px]">
            <div class="flex flex-col gap-1 mb-6 border-b border-slate-200 dark:border-slate-800 pb-6">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">AI Configuration</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Manage your AI model integrations, default
                    behaviors, and API keys.</p>
            </div>

            <!-- Missing Key Warning -->
            <div v-if="!hasOpenRouterKey"
                class="mb-8 rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 p-4 flex items-start gap-4">
                <div
                    class="p-2 bg-white dark:bg-slate-900 rounded-lg text-orange-500 shrink-0 border border-slate-200 dark:border-slate-800">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">Missing OpenRouter Key</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        To enable AI features, you must configure the <code
                            class="px-1 py-0.5 rounded bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 font-mono text-xs">OPENROUTER_KEY</code>
                        inside your <code>.env</code> file.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-8 max-w-3xl">
                <!-- AI Features Section -->
                <section>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">psychology</span>
                        Automated Description Generation
                    </h3>
                    <div
                        class="bg-slate-50 dark:bg-slate-800 p-5 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input v-model="form.use_ai_description" :disabled="!hasOpenRouterKey" type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary disabled:opacity-50 accent-primary" />
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-slate-900 dark:text-white"
                                    :class="{ 'opacity-50': !hasOpenRouterKey }">
                                    Use AI to generate bookmark descriptions
                                </label>
                                <p class="text-slate-500 dark:text-slate-400 mt-1">
                                    When creating a new bookmark, we will attempt to automatically write a summary of
                                    the page using your configured AI model.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="h-px bg-slate-200 dark:bg-slate-800 w-full"></div>

                <!-- AI Model Selection -->
                <section>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">model_training</span>
                        AI Model
                    </h3>
                    <div
                        class="bg-slate-50 dark:bg-slate-800 p-5 rounded-lg border border-slate-200 dark:border-slate-700">
                        <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">Preferred
                            Model</label>
                        <div v-if="aiModels.length === 0"
                            class="h-12 rounded-lg bg-slate-200 dark:bg-slate-700 animate-pulse">
                        </div>
                        <SearchableDropdown v-else v-model="form.ai_model" :options="aiModels"
                            placeholder="Select an AI model..." search-placeholder="Search models..." />
                        <span v-if="form.errors.ai_model" class="text-xs text-red-500 mt-1">{{ form.errors.ai_model
                            }}</span>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                Choose the AI model to use for generating bookmark descriptions.
                            </p>
                            <a href="https://openrouter.ai/models" target="_blank" rel="noopener noreferrer"
                                class="text-xs text-primary hover:underline hover:text-primary/80 inline-flex items-center gap-1 font-medium transition-colors">
                                View Models & Pricing
                                <span class="material-symbols-outlined"
                                    style="font-size: 14px; width: 14px; height: 14px; line-height: 14px;">open_in_new</span>
                            </a>
                        </div>
                    </div>
                </section>

                <div class="h-px bg-slate-200 dark:bg-slate-800 w-full"></div>

                <div class="flex justify-end pt-2">
                    <SubmitButton :processing="form.processing" :disabled="!hasOpenRouterKey" class="!w-auto !px-6">
                        Save Changes
                    </SubmitButton>
                </div>
            </form>
        </div>
    </div>
</template>
