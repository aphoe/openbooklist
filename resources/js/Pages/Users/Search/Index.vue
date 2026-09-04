<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BookmarkCard from '@/Components/Bookmarks/BookmarkCard.vue';
import TagCloudItem from '@/Components/Tags/TagCloudItem.vue';
import InfoBookmarkModal from '@/Components/Modals/InfoBookmarkModal.vue';
import InfoCategoryModal from '@/Components/Modals/InfoCategoryModal.vue';
import InfoTagModal from '@/Components/Modals/InfoTagModal.vue';
import EditBookmarkModal from '@/Components/Modals/EditBookmarkModal.vue';
import EditTagModal from '@/Components/Modals/EditTagModal.vue';
import ConfirmDeleteModal from '@/Components/Modals/ConfirmDeleteModal.vue';
import ConfirmDeleteTagModal from '@/Components/Modals/ConfirmDeleteTagModal.vue';

const props = defineProps({
    query: String,
    bookmarks: Array,
    categories: Array,
    tags: Array,
    allCategories: {
        type: Array,
        default: () => [],
    },
    allTags: {
        type: Array,
        default: () => [],
    },
    tab: {
        type: String,
        default: 'all'
    }
});

const currentTab = ref(props.tab);

const activeDropdown = ref(null);
const toggleDropdown = (id) => { activeDropdown.value = activeDropdown.value === id ? null : id; };
const closeDropdown = () => { activeDropdown.value = null; };

const clickOutsideDropdown = (e) => {
    if (activeDropdown.value && !e.target.closest('.list-dropdown-container')) {
        closeDropdown();
    }
};

const selectedBookmark = ref(null);
const selectedCategory = ref(null);
const selectedTag = ref(null);
const showBookmarkInfoModal = ref(false);
const showBookmarkEditModal = ref(false);
const showBookmarkDeleteModal = ref(false);
const showCategoryInfoModal = ref(false);
const showTagInfoModal = ref(false);
const showTagEditModal = ref(false);
const showTagDeleteModal = ref(false);

const handleBookmarkInfo = (bookmark) => {
    selectedBookmark.value = bookmark;
    showBookmarkInfoModal.value = true;
};
const handleBookmarkEdit = (bookmark) => {
    selectedBookmark.value = bookmark;
    showBookmarkEditModal.value = true;
};
const handleBookmarkDelete = (bookmark) => {
    selectedBookmark.value = bookmark;
    showBookmarkDeleteModal.value = true;
};
const handleBookmarkFavorite = (bookmark) => {
    router.post(route('bookmarks.favorite', bookmark.id), {}, { preserveScroll: true, preserveState: true });
};
const handleBookmarkRefetch = (bookmark) => {
    router.post(route('bookmarks.refetch-metadata', bookmark.id), {}, { preserveScroll: true });
};
const handleBookmarkSetImage = (bookmark) => {
    selectedBookmark.value = bookmark;
    showBookmarkEditModal.value = true;
};

const handleCategoryInfo = (category) => {
    selectedCategory.value = category;
    showCategoryInfoModal.value = true;
};
const handleCategoryEdit = (category) => {
    selectedCategory.value = category;
    // Note: Search doesn't have edit modal for categories, would need to navigate
    // For now, this would require a modal component
};
const handleCategoryDelete = (category) => {
    selectedCategory.value = category;
    // Note: Search doesn't have delete modal for categories
};

const handleTagInfo = (tag) => {
    selectedTag.value = tag;
    showTagInfoModal.value = true;
};
const handleTagEdit = (tag) => {
    selectedTag.value = tag;
    showTagEditModal.value = true;
};
const handleTagDelete = (tag) => {
    selectedTag.value = tag;
    showTagDeleteModal.value = true;
};

const switchTab = (tabValue) => {
    currentTab.value = tabValue;
    router.get(route('search'), { q: props.query, tab: tabValue }, { preserveState: true, replace: true });
};

onMounted(() => {
    document.addEventListener('click', clickOutsideDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', clickOutsideDropdown);
});

</script>

<template>
    <DashboardLayout>

        <Head title="Search Results" />

        <div class="space-y-10 w-full">

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
                                @click="handleBookmarkInfo(bookmark)"
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
                        <div v-if="currentTab === 'bookmarks'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <BookmarkCard
                                v-for="bookmark in bookmarks"
                                :key="bookmark.id"
                                :bookmark="bookmark"
                                @info="handleBookmarkInfo"
                                @edit="handleBookmarkEdit"
                                @delete="handleBookmarkDelete"
                                @favorite="handleBookmarkFavorite"
                                @refetch="handleBookmarkRefetch"
                                @set-image="handleBookmarkSetImage"
                            />
                        </div>
                        <div v-else
                            class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800">
                            <!-- Loop limited to 3 for all tab -->
                            <div v-for="bookmark in bookmarks.slice(0, 3)"
                                :key="bookmark.id" @click="handleBookmarkInfo(bookmark)"
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
                            <div v-if="bookmarks.length > 3" @click="switchTab('bookmarks')"
                                class="p-4 flex items-center justify-center text-primary text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                View all {{ bookmarks.length }} bookmark results
                            </div>
                        </div>
                    </section>

                    <!-- Categories Section -->
                    <section v-if="['all', 'categories'].includes(currentTab) && categories.length > 0">
                        <h3 class="text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-widest mb-4">
                            Categories</h3>
                        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-visible">
                            <table class="w-full text-left border-collapse min-w-full">
                                <thead>
                                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Slug</th>
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Bookmarks</th>
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <tr v-for="category in categories" :key="category.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                                    <span class="material-symbols-outlined text-base">folder</span>
                                                </div>
                                                <div class="flex flex-col overflow-hidden">
                                                    <span class="font-semibold text-sm truncate text-slate-900 dark:text-white">{{ category.name }}</span>
                                                    <span class="text-xs font-mono text-slate-500 dark:text-slate-400 truncate sm:hidden">{{ category.slug }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                            <code class="text-xs font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-600 dark:text-slate-400">{{ category.slug }}</code>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-600 dark:text-slate-400">
                                            <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full text-xs border border-slate-200 dark:border-slate-700">{{ category.bookmarks_count || 0 }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right align-middle">
                                            <div class="relative list-dropdown-container inline-block text-left">
                                                <button @click.stop="toggleDropdown(category.id)" class="text-slate-400 hover:text-primary transition-colors p-1 rounded-full outline-none">
                                                    <span class="material-symbols-outlined text-[20px] block">more_vert</span>
                                                </button>

                                                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                                    <div v-if="activeDropdown === category.id" class="absolute right-0 top-full mt-1 w-36 bg-white dark:bg-slate-800 rounded-md shadow-lg border border-slate-200 dark:border-slate-700 z-[100] py-1 origin-top-right">
                                                        <button @click="(closeDropdown(), handleCategoryInfo(category))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                            <span class="material-symbols-outlined text-[16px]">info</span>
                                                            Info
                                                        </button>
                                                        <button @click="(closeDropdown(), handleCategoryEdit(category))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                                            Edit
                                                        </button>
                                                        <button @click="(closeDropdown(), handleCategoryDelete(category))" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors w-full text-left font-medium">
                                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </transition>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Tags Section -->
                    <section v-if="['all', 'tags'].includes(currentTab) && tags.length > 0">
                        <h3 class="text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-widest mb-4">
                            Tags</h3>
                        <div class="flex flex-wrap gap-4">
                            <TagCloudItem
                                v-for="tag in tags"
                                :key="tag.id"
                                :tag="tag"
                                @info="handleTagInfo"
                                @edit="handleTagEdit"
                                @delete="handleTagDelete"
                            />
                        </div>
                    </section>

                </div>
            </div>
        </div>

        <InfoBookmarkModal :show="showBookmarkInfoModal" :bookmark="selectedBookmark" @close="showBookmarkInfoModal = false" />
        <EditBookmarkModal :show="showBookmarkEditModal" :bookmark="selectedBookmark" :categories="allCategories"
            :tags="allTags" @close="showBookmarkEditModal = false" />
        <ConfirmDeleteModal :show="showBookmarkDeleteModal" :bookmark="selectedBookmark" @close="showBookmarkDeleteModal = false" />
        <InfoCategoryModal :show="showCategoryInfoModal" :category="selectedCategory" @close="showCategoryInfoModal = false" />
        <InfoTagModal :show="showTagInfoModal" :tag="selectedTag" @close="showTagInfoModal = false" />
        <EditTagModal :show="showTagEditModal" :tag="selectedTag" @close="showTagEditModal = false" />
        <ConfirmDeleteTagModal :show="showTagDeleteModal" :tag="selectedTag" @close="showTagDeleteModal = false" />

    </DashboardLayout>
</template>
