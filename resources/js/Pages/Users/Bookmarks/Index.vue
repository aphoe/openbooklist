<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import AddBookmarkModal from '@/Components/Modals/AddBookmarkModal.vue';
import InfoBookmarkModal from '@/Components/Modals/InfoBookmarkModal.vue';
import EditBookmarkModal from '@/Components/Modals/EditBookmarkModal.vue';
import ConfirmDeleteModal from '@/Components/Modals/ConfirmDeleteModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BookmarkCard from '@/Components/Bookmarks/BookmarkCard.vue';

const page = usePage();

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
const viewMode = ref('grid');

const sortMode = ref('newest');

const activeDropdown = ref(null);
const toggleDropdown = (id) => { activeDropdown.value = activeDropdown.value === id ? null : id; };
const closeDropdown = () => { activeDropdown.value = null; };

const clickOutsideDropdown = (e) => {
    if (activeDropdown.value && !e.target.closest('.list-dropdown-container')) {
        closeDropdown();
    }
};

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
const handleRefetch = (bookmark) => {
    router.post(route('bookmarks.refetch-metadata', bookmark.id), {}, { preserveScroll: true });
};

onMounted(() => {
    document.addEventListener('click', clickOutsideDropdown);
    const params = new URLSearchParams(window.location.search);
    if (params.has('sort')) {
        sortMode.value = params.get('sort');
    }
});

onUnmounted(() => {
    document.removeEventListener('click', clickOutsideDropdown);
});

watch(sortMode, (newVal) => {
    const params = new URLSearchParams(window.location.search);

    params.set('sort', newVal);

    router.get(page.url.split('?')[0], Object.fromEntries(params.entries()), {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    });
});
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
            <!-- Header with Title, Sort, View Toggle and Add Button -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">All Bookmarks</h2>
                </div>

                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <!-- Sort -->
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <span>Sort by:</span>
                        <select v-model="sortMode" class="bg-transparent border-none font-medium text-slate-700 dark:text-slate-300 focus:ring-0 cursor-pointer pr-8 py-0">
                            <option value="newest">Date Added (Newest)</option>
                            <option value="oldest">Date Added (Oldest)</option>
                            <option value="alphabetical">Alphabetical</option>
                        </select>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-lg p-1 hidden sm:flex">
                        <button
                            @click="viewMode = 'grid'"
                            :class="['p-1.5 rounded-md transition-all', viewMode === 'grid' ? 'bg-white dark:bg-slate-700 shadow-sm text-primary' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200']">
                            <span class="material-symbols-outlined text-[20px] block">grid_view</span>
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="['p-1.5 rounded-md transition-all', viewMode === 'list' ? 'bg-white dark:bg-slate-700 shadow-sm text-primary' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200']">
                            <span class="material-symbols-outlined text-[20px] block">view_list</span>
                        </button>
                    </div>

                    <button class="bg-primary hover:bg-blue-600 text-white font-semibold flex-1 sm:flex-none h-10 px-4 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2" @click="showAddModal = true">
                        <span class="material-symbols-outlined text-lg">add</span>
                        <span class="truncate">Add Bookmark</span>
                    </button>
                </div>
            </div>

            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Card loop -->
                <BookmarkCard
                    v-for="bookmark in bookmarks.data"
                    :key="bookmark.id"
                    :bookmark="bookmark"
                    @info="handleInfo"
                    @edit="handleEdit"
                    @delete="handleDelete"
                    @favorite="handleFavorite"
                    @refetch="handleRefetch"
                />
            </div>

            <!-- List View -->
            <div v-else class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 w-3/5">Title</th>
                                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 w-[15%] hidden sm:table-cell">Domain</th>
                                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 w-[10%] hidden md:table-cell">Category</th>
                                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 w-[15%] text-right">Date Added</th>
                                <th class="py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="bookmark in bookmarks.data" :key="bookmark.id" class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="py-4 px-4 max-w-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 border border-slate-200 dark:border-slate-700 overflow-hidden">
                                            <img :src="bookmark.image_url" :alt="bookmark.title" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex flex-col overflow-hidden min-w-0 w-full">
                                            <a :href="bookmark.url" target="_blank" class="block text-sm font-semibold text-slate-900 dark:text-white hover:text-primary whitespace-normal break-words">
                                                <span v-if="bookmark.favorite" class="material-symbols-outlined text-[16px] text-red-500 flex-shrink-0" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                                {{ bookmark.title }}
                                            </a>
                                            <span class="text-xs text-slate-500 dark:text-slate-400 sm:hidden truncate">{{ bookmark.domain || bookmark.url }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 hidden sm:table-cell max-w-0">
                                    <a :href="bookmark.url" target="_blank" class="text-sm text-slate-500 dark:text-slate-400 hover:underline hover:text-primary truncate block w-full">
                                        {{ bookmark.domain || bookmark.url }}
                                    </a>
                                </td>
                                <td class="py-4 px-4 hidden md:table-cell">
                                    <div class="flex flex-wrap gap-2">
                                        <span v-if="bookmark.category" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ bookmark.category.name }}
                                        </span>
                                        <span v-else class="text-xs text-slate-400 italic">Uncategorized</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    {{ new Date(bookmark.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) }}
                                </td>
                                <td class="py-4 px-0 text-right align-middle">
                                    <div class="relative list-dropdown-container inline-block text-left">
                                        <button @click.stop="toggleDropdown(bookmark.id)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-opacity p-1 rounded-full outline-none">
                                            <span class="material-symbols-outlined text-[20px] block">more_vert</span>
                                        </button>

                                        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                            <div v-if="activeDropdown === bookmark.id" class="absolute right-0 top-full mt-1 w-36 bg-white dark:bg-slate-800 rounded-md shadow-lg border border-slate-200 dark:border-slate-700 z-[100] py-1 origin-top-right">
                                                <a :href="bookmark.url" target="_blank" @click="closeDropdown" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                                    Visit
                                                </a>
                                                <button @click="(closeDropdown(), handleInfo(bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                    <span class="material-symbols-outlined text-[16px]">info</span>
                                                    Info
                                                </button>
                                                <button @click="(closeDropdown(), handleFavorite(bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                    <span class="material-symbols-outlined text-[16px]">{{ bookmark.favorite ? 'heart_broken' : 'favorite' }}</span>
                                                    {{ bookmark.favorite ? 'Unfavorite' : 'Favorite' }}
                                                </button>
                                                <button @click="(closeDropdown(), handleRefetch(bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                                                    Refetch Metadata
                                                </button>
                                                <button @click="(closeDropdown(), handleEdit(bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                                    Edit
                                                </button>
                                                <button @click="(closeDropdown(), handleDelete(bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors w-full text-left font-medium">
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
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex items-center justify-center" v-if="(bookmarks.meta ? bookmarks.meta.last_page : bookmarks.last_page) > 1">
                <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                    <template v-for="(link, index) in (bookmarks.meta ? bookmarks.meta.links : bookmarks.links)" :key="index">
                        <!-- If it's a link -->
                        <Link v-if="link.url" :href="link.url"
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 border-y border-slate-300 dark:border-slate-700',
                                link.active ? 'z-10 bg-primary text-white border-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary' : 'text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-offset-0',
                                index === 0 ? 'rounded-l-md px-2 border-l' : '',
                                index === (bookmarks.meta ? bookmarks.meta.links.length - 1 : bookmarks.links.length - 1) ? 'rounded-r-md px-2 border-r' : (!link.active ? 'border-r' : '')
                            ]">
                            <span v-if="link.label.includes('Previous')" class="material-symbols-outlined text-[20px]">chevron_left</span>
                            <span v-else-if="link.label.includes('Next')" class="material-symbols-outlined text-[20px]">chevron_right</span>
                            <span v-else v-html="link.label"></span>
                        </Link>
                        <!-- Disabled link -->
                        <span v-else
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-400 border-y border-slate-300 dark:border-slate-700',
                                index === 0 ? 'rounded-l-md px-2 border-l' : '',
                                index === (bookmarks.meta ? bookmarks.meta.links.length - 1 : bookmarks.links.length - 1) ? 'rounded-r-md px-2 border-r' : 'border-r'
                            ]">
                            <span v-if="link.label.includes('Previous')" class="material-symbols-outlined text-[20px]">chevron_left</span>
                            <span v-else-if="link.label.includes('Next')" class="material-symbols-outlined text-[20px]">chevron_right</span>
                            <span v-else v-html="link.label"></span>
                        </span>
                    </template>
                </nav>
            </div>
        </div>

        <AddBookmarkModal
            :show="showAddModal"
            :categories="allCategories"
            :tags="allTags"
            @close="showAddModal = false"
        />

        <InfoBookmarkModal
            :show="showInfoModal"
            :bookmark="selectedBookmark"
            @close="showInfoModal = false"
        />

        <EditBookmarkModal
            :show="showEditModal"
            :bookmark="selectedBookmark"
            :categories="allCategories"
            :tags="allTags"
            @close="showEditModal = false"
        />

        <ConfirmDeleteModal
            :show="showDeleteModal"
            :bookmark="selectedBookmark"
            @close="showDeleteModal = false"
        />
    </DashboardLayout>
</template>
