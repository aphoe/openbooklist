<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InfoBookmarkModal from '@/Components/Modals/InfoBookmarkModal.vue';
import EditBookmarkModal from '@/Components/Modals/EditBookmarkModal.vue';
import ConfirmDeleteModal from '@/Components/Modals/ConfirmDeleteModal.vue';

const page = usePage();

const props = defineProps({
    query: String,
    bookmarks: Array,
    categories: Array,
    tags: Array,
    tab: {
        type: String,
        default: 'all'
    }
});

const currentTab = ref(props.tab);

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

const switchTab = (tabValue) => {
    currentTab.value = tabValue;
    router.get(route('search'), { q: props.query, tab: tabValue }, { preserveState: true, replace: true });
};

</script>

<template>
    <DashboardLayout>

        <Head title="Search Results" />

        <div class="p-8 max-w-7xl mx-auto space-y-10 w-full overflow-y-auto">

            <div v-if="query.length === 0"
                class="flex flex-col items-center justify-center p-20 text-center text-slate-500">
                <span class="material-symbols-outlined text-[48px] text-slate-300 mb-4">search</span>
                <p class="text-lg">Type something in the search bar above to begin.</p>
            </div>
            <div v-else>
                <!-- Context header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold">Search results for "{{ query }}"</h1>
                </div>

                <!-- Tabbed Results Toggles -->
                <div class="border-b border-slate-200 dark:border-slate-800 mb-8">
                    <div class="flex gap-8 overflow-x-auto scrollbar-hide">
                        <button @click="switchTab('all')"
                            :class="['flex flex-col items-center justify-center border-b-2 pb-3 pt-2 outline-none', currentTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-slate-100']">
                            <p class="text-sm font-bold tracking-tight px-2 whitespace-nowrap">All</p>
                        </button>
                        <button @click="switchTab('bookmarks')"
                            :class="['flex flex-col items-center justify-center border-b-2 pb-3 pt-2 outline-none', currentTab === 'bookmarks' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-slate-100']">
                            <p class="text-sm font-bold tracking-tight px-2 whitespace-nowrap">Bookmarks ({{
                                bookmarks.length }})</p>
                        </button>
                        <button @click="switchTab('categories')"
                            :class="['flex flex-col items-center justify-center border-b-2 pb-3 pt-2 outline-none', currentTab === 'categories' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-slate-100']">
                            <p class="text-sm font-bold tracking-tight px-2 whitespace-nowrap">Categories ({{
                                categories.length }})</p>
                        </button>
                        <button @click="switchTab('tags')"
                            :class="['flex flex-col items-center justify-center border-b-2 pb-3 pt-2 outline-none', currentTab === 'tags' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-slate-100']">
                            <p class="text-sm font-bold tracking-tight px-2 whitespace-nowrap">Tags ({{ tags.length }})
                            </p>
                        </button>
                    </div>
                </div>

                <!-- Content Sections -->
                <div class="space-y-10">

                    <!-- EMPTY STATE IF NO RESULTS AT ALL -->
                    <div v-if="bookmarks.length === 0 && categories.length === 0 && tags.length === 0"
                        class="text-center py-10">
                        <span
                            class="material-symbols-outlined text-border-slate-300 text-6xl text-slate-300 mb-2">sentiment_dissatisfied</span>
                        <p class="text-slate-500 text-lg">No matches found for "{{ query }}"</p>
                    </div>

                    <!-- Top Hits Section -->
                    <section v-if="['all'].includes(currentTab) && bookmarks.length > 0">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-slate-900 dark:text-slate-100 text-xl font-bold">Top Hits</h2>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Most
                                Relevant</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Show top 2 bookmarks as hits -->
                            <div v-for="bookmark in bookmarks.slice(0, 2)" :key="bookmark.id"
                                @click="handleInfo(bookmark)"
                                class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex gap-4 hover:border-primary/50 transition-colors cursor-pointer group block">
                                <div
                                    class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0 overflow-hidden">
                                    <img v-if="bookmark.image_url" :src="bookmark.image_url"
                                        class="object-cover w-full h-full" />
                                    <span v-else class="material-symbols-outlined text-[28px]">link</span>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h3 class="text-slate-900 dark:text-slate-100 font-semibold text-base truncate">{{
                                        bookmark.title }}</h3>
                                    <p class="text-slate-500 text-sm truncate">{{ bookmark.domain || bookmark.url }}</p>
                                    <div class="flex gap-2 mt-2">
                                        <span
                                            class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 uppercase">Bookmark</span>
                                        <span v-if="bookmark.category"
                                            class="px-2 py-0.5 rounded bg-primary/10 text-[10px] font-bold text-primary uppercase">{{
                                                bookmark.category.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Bookmarks Results -->
                    <section v-if="['all', 'bookmarks'].includes(currentTab) && bookmarks.length > 0">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-widest">
                                Bookmarks</h3>
                            <button v-if="currentTab === 'all' && bookmarks.length > 3" @click="switchTab('bookmarks')"
                                class="text-xs text-primary font-bold hover:underline">View all
                                {{ bookmarks.length }}</button>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800">
                            <!-- Loop full list if tab is bookmarks, else limit to 3 -->
                            <div v-for="bookmark in (currentTab === 'bookmarks' ? bookmarks : bookmarks.slice(0, 3))"
                                :key="bookmark.id" @click="handleInfo(bookmark)"
                                class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer group">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div
                                        class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                                        <span class="material-symbols-outlined">link</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <h4 class="text-slate-900 dark:text-slate-100 font-medium truncate">{{
                                            bookmark.title }}</h4>
                                        <p class="text-slate-400 text-xs truncate">{{ bookmark.domain || bookmark.url }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">arrow_forward</span>
                            </div>
                            <div v-if="currentTab === 'all' && bookmarks.length > 3" @click="switchTab('bookmarks')"
                                class="p-4 flex items-center justify-center text-primary text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                View all {{ bookmarks.length }} bookmark results
                            </div>
                        </div>
                    </section>

                    <!-- Categories and Tags Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8"
                        v-if="['all', 'categories', 'tags'].includes(currentTab)">
                        <!-- Categories Section -->
                        <section v-if="['all', 'categories'].includes(currentTab) && categories.length > 0">
                            <h3
                                class="text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-widest mb-4">
                                Categories</h3>
                            <div class="flex flex-col gap-2">
                                <Link :href="route('dashboard', { category: category.slug })"
                                    v-for="category in categories" :key="category.id"
                                    class="flex items-center justify-between p-3 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-all group block">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-primary">folder_open</span>
                                        <span class="text-slate-900 dark:text-slate-100 font-medium">{{ category.name
                                        }}</span>
                                    </div>
                                    <span
                                        class="text-xs text-slate-400 font-bold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full group-hover:bg-primary/10 group-hover:text-primary transition-colors">{{
                                            category.bookmarks_count }} items</span>
                                </Link>
                            </div>
                        </section>

                        <!-- Tags Section -->
                        <section v-if="['all', 'tags'].includes(currentTab) && tags.length > 0">
                            <h3
                                class="text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-widest mb-4">
                                Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                <Link :href="route('dashboard', { tag: tag.slug })" v-for="tag in tags" :key="tag.id"
                                    class="px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-medium border border-transparent hover:border-slate-300 transition-colors group">
                                    #{{ tag.name }} <span
                                        class="bg-slate-400 dark:bg-slate-600 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold ml-1 group-hover:bg-slate-500 transition-colors">{{
                                            tag.bookmarks_count }}</span>
                                </Link>
                            </div>
                        </section>
                    </div>

                </div>
            </div>
        </div>

        <InfoBookmarkModal :show="showInfoModal" :bookmark="selectedBookmark" @close="showInfoModal = false" />
        <EditBookmarkModal :show="showEditModal" :bookmark="selectedBookmark" :categories="allCategories"
            :tags="allTags" @close="showEditModal = false" />
        <ConfirmDeleteModal :show="showDeleteModal" :bookmark="selectedBookmark" @close="showDeleteModal = false" />

    </DashboardLayout>
</template>
