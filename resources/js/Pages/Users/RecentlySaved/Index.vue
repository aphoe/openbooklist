<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BookmarkCard from '@/Components/Bookmarks/BookmarkCard.vue';
import InfoBookmarkModal from '@/Components/Modals/InfoBookmarkModal.vue';
import EditBookmarkModal from '@/Components/Modals/EditBookmarkModal.vue';
import ConfirmDeleteModal from '@/Components/Modals/ConfirmDeleteModal.vue';

const page = usePage();

defineProps({
    bookmarks: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    tags: {
        type: Array,
        default: () => [],
    },
});

const allCategories = computed(() => page.props.categories || []);
const allTags = computed(() => page.props.tags || []);

const selectedBookmark = ref(null);
const showInfoModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const handleInfo = (bookmark) => {
    selectedBookmark.value = bookmark;
    showInfoModal.value = true;
};
const handleEdit = (bookmark) => {
    selectedBookmark.value = bookmark;
    showEditModal.value = true;
};
const handleDelete = (bookmark) => {
    selectedBookmark.value = bookmark;
    showDeleteModal.value = true;
};
const handleFavorite = (bookmark) => {
    router.post(route('bookmarks.favorite', bookmark.id), {}, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <DashboardLayout>

        <Head title="Recently Saved" />

        <div class="p-8 max-w-7xl mx-auto space-y-10 w-full overflow-y-auto">
            <!-- Activity Feed Intro -->
            <div class="space-y-1">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Recently Saved</h1>
                <p class="text-slate-500 dark:text-slate-400">Your latest discoveries and knowledge assets.</p>
            </div>

            <!-- Section 1: Recently Saved Bookmarks -->
            <section class="space-y-4" v-if="bookmarks.length > 0">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                    <h2 class="text-xl font-bold flex items-center gap-2 text-slate-900 dark:text-white">
                        <span class="material-symbols-outlined text-primary">link</span>
                        Recently Saved Bookmarks
                    </h2>
                    <Link :href="route('dashboard')" class="text-primary text-sm font-semibold hover:underline">View all
                    </Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                    <BookmarkCard v-for="bookmark in bookmarks" :key="bookmark.id" :bookmark="bookmark"
                        @info="handleInfo" @edit="handleEdit" @delete="handleDelete" @favorite="handleFavorite" />
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Section 2: Recently Created Categories -->
                <section class="space-y-4" v-if="categories.length > 0">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <h2 class="text-xl font-bold flex items-center gap-2 text-slate-900 dark:text-white">
                            <span class="material-symbols-outlined text-primary">folder_open</span>
                            New Categories
                        </h2>
                        <Link :href="route('categories.index')"
                            class="text-primary text-sm font-semibold hover:underline">View all</Link>
                    </div>
                    <div class="space-y-2">
                        <div v-for="category in categories" :key="category.id"
                            class="flex items-center justify-between p-3 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 transition-colors group block">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">folder</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ category.name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Recently Added Tags -->
                <section class="space-y-4" v-if="tags.length > 0">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <h2 class="text-xl font-bold flex items-center gap-2 text-slate-900 dark:text-white">
                            <span class="material-symbols-outlined text-primary">label</span>
                            Recent Tags
                        </h2>
                        <Link :href="route('tags.index')" class="text-primary text-sm font-semibold hover:underline">
                            View all</Link>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-2">
                        <div v-for="tag in tags" :key="tag.id"
                            class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-transparent rounded-full transition-all group">
                            <span class="text-sm font-medium">{{ tag.name }}</span>
                            <span
                                class="bg-slate-400 dark:bg-slate-600 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{
                                tag.bookmarks_count }}</span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Empty State for Everything -->
            <div v-if="bookmarks.length === 0 && categories.length === 0 && tags.length === 0"
                class="flex flex-col items-center justify-center py-20 text-center">
                <div
                    class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 text-slate-400">
                    <span class="material-symbols-outlined text-[32px]">history</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No Recent Activity</h3>
                <p class="text-slate-500 max-w-sm">You haven't added any bookmarks, categories, or tags yet.</p>
            </div>
        </div>

        <InfoBookmarkModal :show="showInfoModal" :bookmark="selectedBookmark" @close="showInfoModal = false" />

        <EditBookmarkModal :show="showEditModal" :bookmark="selectedBookmark" :categories="allCategories"
            :tags="allTags" @close="showEditModal = false" />

        <ConfirmDeleteModal :show="showDeleteModal" :bookmark="selectedBookmark" @close="showDeleteModal = false" />
    </DashboardLayout>
</template>
