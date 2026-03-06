<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Sidebar from '@/Components/Menus/Sidebar.vue';
import FlashMessages from '@/Components/FlashMessages.vue';

const page = usePage();
const authUser = page.props.auth?.user;

const globalSearchQuery = ref(new URLSearchParams(window.location.search).get('q') || '');

const handleGlobalSearch = () => {
    if (globalSearchQuery.value.trim().length > 0) {
        router.get(route('search'), { q: globalSearchQuery.value }, { preserveState: false, replace: false });
    }
};
</script>

<template>
    <div
        class="relative flex h-screen w-full flex-row overflow-hidden font-display text-slate-900 dark:text-slate-100 bg-background-light dark:bg-background-dark">
        <!-- Sidebar -->
        <Sidebar class="h-full" />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full bg-background-light dark:bg-background-dark overflow-x-hidden">
            <!-- Top Header Bar -->
            <header
                class="sticky top-0 z-20 bg-white/80 dark:bg-[#151c2a]/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center max-w-[1400px] mx-auto w-full">
                    <!-- Search -->
                    <div class="w-full sm:max-w-md relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-slate-400">search</span>
                        </div>
                        <input v-model="globalSearchQuery" @keyup.enter="handleGlobalSearch"
                            class="block w-full pl-10 pr-10 py-2.5 border-none rounded-lg leading-5 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary/50 sm:text-sm transition-all"
                            placeholder="Search..." type="text" />
                        <button v-if="globalSearchQuery.length > 0" @click="globalSearchQuery = ''"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content Slot -->
            <div class="flex-1 p-6 overflow-y-auto">
                <FlashMessages />
                <div class="max-w-[1400px] mx-auto">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
