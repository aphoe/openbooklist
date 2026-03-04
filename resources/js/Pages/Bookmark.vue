<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AddBookmarkModal from '@/Components/Modals/AddBookmarkModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

defineProps({
    bookmarks: Object,
    allCategories: {
        type: Array,
        default: () => [],
    },
    allTags: {
        type: Array,
        default: () => [],
    },
});

const showAddModal = ref(false);
</script>

<template>
    <DashboardLayout>
        <Head title="Bookmarks" />

        <div v-if="!bookmarks?.data?.length" class="flex flex-col items-center justify-center p-8 h-[calc(100vh-130px)] w-full">
            <div class="max-w-xl w-full flex flex-col items-center text-center">
                <!-- Feature Graphic -->
                <div class="relative w-full h-40 md:h-48 mb-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center justify-center overflow-hidden">
                    <!-- Abstract Graphic Representation -->
                    <div class="relative w-full h-full flex items-center justify-center">
                        <!-- Link Icon Side -->
                        <div class="absolute left-10 md:left-20 flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center shadow-lg border border-primary/20">
                                <span class="material-symbols-outlined text-primary text-[32px]">link</span>
                            </div>
                            <div class="w-24 h-2 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                            <div class="w-16 h-2 bg-slate-100 dark:bg-slate-800 rounded-full"></div>
                        </div>
                        <!-- Animated Transformation Indicator (Static) -->
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-primary/40"></div>
                            <div class="w-3 h-3 rounded-full bg-primary/60"></div>
                            <div class="w-4 h-4 rounded-full bg-primary"></div>
                            <span class="material-symbols-outlined text-primary text-[32px]">auto_awesome</span>
                            <div class="w-4 h-4 rounded-full bg-primary"></div>
                            <div class="w-3 h-3 rounded-full bg-primary/60"></div>
                            <div class="w-2 h-2 rounded-full bg-primary/40"></div>
                        </div>
                        <!-- Bullet Points Side -->
                        <div class="absolute right-10 md:right-20 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                <div class="w-24 h-3 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                <div class="w-32 h-3 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                <div class="w-28 h-3 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                            </div>
                        </div>
                        <!-- Background Pattern Decor -->
                        <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary via-transparent to-transparent">
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <div class="space-y-4">
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">Save and Organize</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed max-w-md mx-auto">
                        Save articles, news, research papers, and blog posts. Ready to try?
                    </p>
                    <div class="pt-6">
                        <button class="bg-primary hover:bg-primary/90 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center gap-2 mx-auto" @click="showAddModal = true">
                            <span class="material-symbols-outlined text-xl">add_link</span>
                            Add First Link
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">All Bookmarks</h2>
                <button class="bg-primary hover:bg-primary/90 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-all flex items-center gap-2" @click="showAddModal = true">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Add Bookmark
                </button>
            </div>
            <!-- Future grid goes here -->
        </div>

        <AddBookmarkModal
            :show="showAddModal"
            :categories="allCategories"
            :tags="allTags"
            @close="showAddModal = false"
        />
    </DashboardLayout>
</template>
