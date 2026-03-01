<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const authUser = page.props.auth?.user;

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post(route('logout'));
};
</script>

<template>
    <div class="relative flex min-h-screen w-full flex-row overflow-x-hidden font-display text-slate-900 dark:text-slate-100 bg-background-light dark:bg-background-dark">
        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-[#151c2a] flex flex-col h-screen fixed left-0 top-0 z-30 hidden md:flex">
            <div class="p-6 flex flex-col h-full">
                <!-- User Profile / Branding -->
                <div class="flex items-center gap-3 mb-8">
                    <img :src="'/assets/images/logo.png'" alt="Logo" class="h-10 w-auto flex-shrink-0" />
                    <div class="flex flex-col overflow-hidden">
                        <h1 class="text-slate-900 dark:text-slate-100 text-base font-semibold leading-normal truncate">OpenBooklist</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-normal truncate">Bookmark Manager</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex flex-col gap-2 flex-1 overflow-y-auto">
                    <Link :href="route('dashboard')" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-xl">dashboard</span>
                        <p class="text-sm font-medium leading-normal">Dashboard</p>
                    </Link>
                    <Link href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined text-xl">bookmark</span>
                        <p class="text-sm font-medium leading-normal">All Bookmarks</p>
                    </Link>
                </nav>

                <!-- CTA Button / Logout -->
                <div class="pt-4 mt-4 border-t border-slate-200 dark:border-slate-800">
                    <div class="mb-4 text-sm font-medium text-slate-700 dark:text-slate-300 px-3">
                        <span class="truncate block">{{ authUser?.first_name }} {{ authUser?.last_name }}</span>
                        <span class="text-xs text-slate-500 font-normal truncate block">{{ authUser?.email }}</span>
                    </div>

                    <form @submit.prevent="logout">
                        <button type="submit" class="flex w-full cursor-pointer items-center justify-center rounded-lg h-10 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold leading-normal transition-colors shadow-sm">
                            <span class="material-symbols-outlined mr-2 text-lg">logout</span>
                            <span class="truncate">Log Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-h-screen md:ml-64 bg-background-light dark:bg-background-dark">
            <!-- Top Header Bar -->
            <header class="sticky top-0 z-20 bg-white/80 dark:bg-[#151c2a]/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center max-w-[1400px] mx-auto w-full">
                    <!-- Search -->
                    <div class="w-full sm:max-w-md relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-slate-400">search</span>
                        </div>
                        <input class="block w-full pl-10 pr-3 py-2.5 border-none rounded-lg leading-5 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary/50 sm:text-sm transition-all" placeholder="Search..." type="text"/>
                    </div>
                </div>
            </header>

            <!-- Page Content Slot -->
            <div class="flex-1 p-6 overflow-y-auto">
                <div class="max-w-[1400px] mx-auto">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
