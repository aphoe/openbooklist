<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import TagCloudItem from '@/Components/Tags/TagCloudItem.vue';
import AddTagModal from '@/Components/Modals/AddTagModal.vue';
import EditTagModal from '@/Components/Modals/EditTagModal.vue';
import InfoTagModal from '@/Components/Modals/InfoTagModal.vue';
import ConfirmDeleteTagModal from '@/Components/Modals/ConfirmDeleteTagModal.vue';

const page = usePage();

const props = defineProps({
    tags: Object,
});

const showAddModal = ref(false);
const showInfoModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedTag = ref(null);

const handleInfo = (tag) => {
    selectedTag.value = tag;
    showInfoModal.value = true;
};

const handleEdit = (tag) => {
    selectedTag.value = tag;
    showEditModal.value = true;
};

const handleDelete = (tag) => {
    selectedTag.value = tag;
    showDeleteModal.value = true;
};

// Group tags by the first letter of their name
const groupedTags = computed(() => {
    const groups = {};
    const tagsArray = props.tags?.data || [];

    tagsArray.forEach(tag => {
        const firstLetter = tag.name.charAt(0).toUpperCase();
        if (!groups[firstLetter]) {
            groups[firstLetter] = [];
        }
        groups[firstLetter].push(tag);
    });

    // Sort keys alphabetically
    return Object.keys(groups).sort().reduce((acc, key) => {
        acc[key] = groups[key];
        return acc;
    }, {});
});

// Setup current sort handling and search for filtering UI
const searchParams = new URL(window.location.href).searchParams;
const search = ref(searchParams.get('search') || '');
const currentSort = ref(searchParams.get('sort') || 'alphabetical');

const updateFilters = () => {
    const params = {};
    if (currentSort.value !== 'alphabetical') {
        params.sort = currentSort.value;
    }
    if (search.value) {
        params.search = search.value;
    }

    router.get(route('tags.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

let searchTimeout;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateFilters();
    }, 300);
});

const updateSort = (sort) => {
    currentSort.value = sort;
    updateFilters();
};
</script>

<template>
    <DashboardLayout>

        <Head title="Tags" />

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Filter -->
            <aside
                class="w-full md:w-64 lg:w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl flex flex-col shrink-0 overflow-hidden h-fit">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Filters</h3>
                    <!-- Search Filter -->
                    <div class="relative w-full mb-4">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </span>
                        <input v-model="search"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block pl-10 p-2.5"
                            placeholder="Search tags..." type="text" />
                    </div>

                </div>

                <div class="p-5">
                    <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">
                        Sort By</h4>
                    <div class="space-y-2">
                        <label
                            class="flex items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer">
                            <input :checked="currentSort === 'alphabetical'" @change="updateSort('alphabetical')"
                                class="w-4 h-4 text-primary bg-slate-100 border-slate-300 focus:ring-primary dark:focus:ring-primary dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600"
                                name="sort" type="radio" />
                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Alphabetical (A-Z)</span>
                        </label>
                        <label
                            class="flex items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer">
                            <input :checked="currentSort === 'popularity'" @change="updateSort('popularity')"
                                class="w-4 h-4 text-primary bg-slate-100 border-slate-300 focus:ring-primary dark:focus:ring-primary dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600"
                                name="sort" type="radio" />
                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Popularity (High to
                                Low)</span>
                        </label>
                        <label
                            class="flex items-center p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer">
                            <input :checked="currentSort === 'recent'" @change="updateSort('recent')"
                                class="w-4 h-4 text-primary bg-slate-100 border-slate-300 focus:ring-primary dark:focus:ring-primary dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600"
                                name="sort" type="radio" />
                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Recently Created</span>
                        </label>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-2">Visual Tag
                            Cloud</h1>
                        <p class="text-slate-600 dark:text-slate-400">Manage your taxonomy visually. Larger tags
                            indicate higher bookmark usage.</p>
                    </div>

                    <button @click="showAddModal = true"
                        class="bg-primary hover:bg-primary/90 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 transition-all flex-shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-lg">add</span>
                        New Tag
                    </button>
                </div>

                <!-- Empty State -->
                <div v-if="!tags?.data?.length"
                    class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-12 text-center shadow-sm">
                    <div
                        class="size-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl">sell</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No Tags Found</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">You haven't created any tags yet or they don't
                        match your filters.</p>
                    <button @click="showAddModal = true"
                        class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Create Your First Tag
                    </button>
                </div>

                <!-- Tag Cloud Sections -->
                <div v-else>
                    <div v-for="(groupTags, letter) in groupedTags" :key="letter" class="mb-10">
                        <div class="flex items-center gap-4 mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                            <h2 class="text-2xl font-bold text-primary">{{ letter }}</h2>
                            <span
                                class="text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">
                                {{ groupTags.length }} tag{{ groupTags.length !== 1 ? 's' : '' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <TagCloudItem v-for="tag in groupTags" :key="tag.id" :tag="tag" @info="handleInfo"
                                @edit="handleEdit" @delete="handleDelete" />
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-10 flex items-center justify-center"
                    v-if="(tags.meta ? tags.meta.last_page : tags.last_page) > 1">
                    <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                        <template v-for="(link, index) in (tags.meta ? tags.meta.links : tags.links)" :key="index">
                            <Link v-if="link.url" :href="link.url" :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 border-y border-slate-300 dark:border-slate-700 transition-colors',
                                link.active ? 'z-10 bg-primary text-white border-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary' : 'text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-offset-0 bg-white dark:bg-slate-900',
                                index === 0 ? 'rounded-l-md px-2 border-l' : '',
                                index === (tags.meta ? tags.meta.links.length - 1 : tags.links.length - 1) ? 'rounded-r-md px-2 border-r' : (!link.active ? 'border-r' : '')
                            ]">
                                <span v-if="link.label.includes('Previous')"
                                    class="material-symbols-outlined text-[20px]">chevron_left</span>
                                <span v-else-if="link.label.includes('Next')"
                                    class="material-symbols-outlined text-[20px]">chevron_right</span>
                                <span v-else v-html="link.label"></span>
                            </Link>
                            <span v-else :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-400 bg-white dark:bg-slate-900 border-y border-slate-300 dark:border-slate-700',
                                index === 0 ? 'rounded-l-md px-2 border-l' : '',
                                index === (tags.meta ? tags.meta.links.length - 1 : tags.links.length - 1) ? 'rounded-r-md px-2 border-r' : 'border-r'
                            ]">
                                <span v-if="link.label.includes('Previous')"
                                    class="material-symbols-outlined text-[20px]">chevron_left</span>
                                <span v-else-if="link.label.includes('Next')"
                                    class="material-symbols-outlined text-[20px]">chevron_right</span>
                                <span v-else v-html="link.label"></span>
                            </span>
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        <AddTagModal :show="showAddModal" @close="showAddModal = false" />
        <InfoTagModal :show="showInfoModal" :tag="selectedTag" @close="showInfoModal = false" />
        <EditTagModal :show="showEditModal" :tag="selectedTag" @close="showEditModal = false" />
        <ConfirmDeleteTagModal :show="showDeleteModal" :tag="selectedTag" @close="showDeleteModal = false" />
    </DashboardLayout>
</template>
