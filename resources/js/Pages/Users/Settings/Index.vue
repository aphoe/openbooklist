<script setup>
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import GeneralSettings from './Partials/GeneralSettings.vue';
import AiSettings from './Partials/AiSettings.vue';
import AccessTokens from './Partials/AccessTokens.vue';

const props = defineProps({
    tab: {
        type: String,
        default: 'general',
    },
});

const switchTab = (newTab) => {
    router.visit(route('settings.index', { tab: newTab }), { preserveState: true, replace: true });
};
</script>

<template>
    <DashboardLayout>

        <Head title="Settings" />

        <!-- Page Header -->
        <div class="mb-8 text-center sm:text-left">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Settings &amp; Integrations</h1>
            <p class="text-slate-500 dark:text-slate-400">Manage your account settings, API keys, and system
                preferences.</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-slate-200 dark:border-slate-800 mb-8">
            <nav aria-label="Tabs" class="-mb-px flex space-x-8 overflow-x-auto">
                <button @click="switchTab('general')" :class="[
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                    tab === 'general' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:border-slate-700'
                ]">
                    General
                </button>
                <button @click="switchTab('tokens')" :class="[
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                    tab === 'tokens' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:border-slate-700'
                ]">
                    Access Tokens
                </button>
                <button @click="switchTab('ai')" :class="[
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                    tab === 'ai' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:border-slate-700'
                ]">
                    AI Configuration
                </button>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div>
            <GeneralSettings v-if="tab === 'general'" />
            <AiSettings v-if="tab === 'ai'" />
            <AccessTokens v-if="tab === 'tokens'" />
        </div>
    </DashboardLayout>
</template>
