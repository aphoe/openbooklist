<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const authUser = computed(() => page.props.auth?.user);
const categories = computed(() => page.props.categories || []);

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post(route('logout'));
};
</script>

<template>
    <div
        class="hidden w-64 flex-col border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-background-dark md:flex sticky top-0 h-full flex-shrink-0">
        <!-- Brand/Logo Area -->
        <div class="flex h-16 items-center gap-3 px-6 border-b border-slate-100 dark:border-slate-800 flex-shrink-0">
            <img :src="'/assets/images/logo.png'" alt="Logo" class="h-8 w-auto flex-shrink-0" />
            <h1 class="text-slate-900 dark:text-white text-lg font-bold tracking-tight truncate">OpenBooklist</h1>
        </div>

        <!-- Links Area -->
        <nav class="flex flex-col gap-2 p-4 flex-1 overflow-y-auto">
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Menu
            </p>

            <Link :href="route('dashboard')" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-colors"
                :class="route().current('dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'">
                <span class="material-symbols-outlined text-xl"
                    :class="{ 'fill-1': route().current('dashboard') }">bookmark</span>
                <span class="text-sm font-medium leading-normal">All Bookmarks</span>
            </Link>

            <Link :href="route('recently-saved')"
                class="flex items-center gap-3 rounded-lg px-3 py-2 transition-colors text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="route().current('recently-saved') ? 'bg-primary/10 text-primary' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'">
                <span class="material-symbols-outlined text-xl"
                    :class="{ 'fill-1': route().current('recently-saved') }">history</span>
                <span class="text-sm font-medium leading-normal">Recently Saved</span>
            </Link>

            <Link :href="route('categories.index')"
                class="flex items-center gap-3 rounded-lg px-3 py-2 transition-colors"
                :class="route().current('categories.*') ? 'bg-primary/10 text-primary' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'">
                <span class="material-symbols-outlined text-xl"
                    :class="{ 'fill-1': route().current('categories.*') }">folder</span>
                <span class="text-sm font-medium leading-normal">Categories</span>
            </Link>

            <Link :href="route('tags.index')" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-colors"
                :class="route().current('tags.*') ? 'bg-primary/10 text-primary' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'">
                <span class="material-symbols-outlined text-xl"
                    :class="{ 'fill-1': route().current('tags.*') }">label</span>
                <span class="text-sm font-medium leading-normal">Tags</span>
            </Link>

            <div class="mt-4" v-if="categories.length > 0">
                <div class="flex items-center justify-between px-3 mb-2">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Top
                        Categories</span>
                    <span
                        class="material-symbols-outlined text-slate-400 text-lg cursor-pointer hover:text-primary">add</span>
                </div>

                <div class="flex flex-col gap-1">
                    <Link v-for="category in categories" :key="category.id"
                        :href="route('dashboard', { category: category.slug })"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="text-sm font-medium leading-normal">{{ category.name }}</span>
                        <span v-if="category.bookmarks_count > 0"
                            class="ml-auto bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 py-0.5 px-2 rounded-full text-xs">{{
                                category.bookmarks_count }}</span>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Footer Area -->
        <div class="mt-auto p-4 border-t border-slate-100 dark:border-slate-800 flex-shrink-0">
            <Link href="#"
                class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-xl">settings</span>
                <span class="text-sm font-medium leading-normal">Settings</span>
            </Link>

            <div class="flex items-center gap-3 mt-4 px-2 py-3">
                <div
                    class="flex items-center justify-center size-10 rounded-full bg-primary/10 text-primary font-bold flex-shrink-0">
                    {{ authUser?.first_name?.charAt(0) || '' }}{{ authUser?.last_name?.charAt(0) || '' }}
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ authUser?.first_name
                        }} {{ authUser?.last_name }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ authUser?.email }}</span>
                </div>
            </div>

            <form @submit.prevent="logout" class="mt-2 px-2">
                <button type="submit"
                    class="flex w-full cursor-pointer items-center justify-center rounded-lg h-10 px-4 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-sm font-bold leading-normal transition-colors shadow-sm">
                    <span class="material-symbols-outlined mr-2 text-lg">logout</span>
                    <span class="truncate">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</template>
