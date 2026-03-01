<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
</script>

<template>
    <DashboardLayout>
        <Head title="Dashboard" />

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Dashboard Overview</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#1e293b] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <span class="material-symbols-outlined block text-2xl">check_circle</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</h3>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">Logged In Successfully</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-[#1e293b] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg bg-primary/10 text-primary">
                        <span class="material-symbols-outlined block text-2xl">bookmark</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Bookmarks</h3>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">0</p>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
