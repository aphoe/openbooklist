<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import AddCategoryModal from '@/Components/Modals/AddCategoryModal.vue';
import InfoCategoryModal from '@/Components/Modals/InfoCategoryModal.vue';
import EditCategoryModal from '@/Components/Modals/EditCategoryModal.vue';
import ConfirmDeleteCategoryModal from '@/Components/Modals/ConfirmDeleteCategoryModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const page = usePage();

const props = defineProps({
    categories: Object,
});

const showAddModal = ref(false);
const showInfoModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const selectedCategory = ref(null);

const activeDropdown = ref(null);
const toggleDropdown = (id) => { activeDropdown.value = activeDropdown.value === id ? null : id; };
const closeDropdown = () => { activeDropdown.value = null; };

const clickOutsideDropdown = (e) => {
    if (activeDropdown.value && !e.target.closest('.list-dropdown-container')) {
        closeDropdown();
    }
};

const handleInfo = (category) => { 
    selectedCategory.value = category; 
    showInfoModal.value = true; 
};
const handleEdit = (category) => { 
    selectedCategory.value = category; 
    showEditModal.value = true; 
};
const handleDelete = (category) => { 
    selectedCategory.value = category; 
    showDeleteModal.value = true; 
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
        <Head title="Categories" />

        <div v-if="!categories?.data?.length" class="flex flex-col items-center justify-center p-8 h-[calc(100vh-130px)] w-full">
            <div class="max-w-xl w-full flex flex-col items-center text-center">
                <!-- Feature Graphic -->
                <div class="relative w-full h-40 md:h-48 mb-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center justify-center overflow-hidden">
                    <div class="relative w-full h-full flex items-center justify-center">
                        <div class="absolute left-10 md:left-20 flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center shadow-lg border border-primary/20">
                                <span class="material-symbols-outlined text-primary text-[32px]">folder</span>
                            </div>
                            <div class="w-24 h-2 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                            <div class="w-16 h-2 bg-slate-100 dark:bg-slate-800 rounded-full"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-primary/40"></div>
                            <div class="w-3 h-3 rounded-full bg-primary/60"></div>
                            <div class="w-4 h-4 rounded-full bg-primary"></div>
                            <span class="material-symbols-outlined text-primary text-[32px]">auto_awesome</span>
                            <div class="w-4 h-4 rounded-full bg-primary"></div>
                            <div class="w-3 h-3 rounded-full bg-primary/60"></div>
                            <div class="w-2 h-2 rounded-full bg-primary/40"></div>
                        </div>
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
                        <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary via-transparent to-transparent">
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <div class="space-y-4">
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">Organize Your Links</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed max-w-md mx-auto">
                        Create categories to logically organize all your bookmarks for quicker access.
                    </p>
                    <div class="pt-6">
                        <button class="bg-primary hover:bg-primary/90 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center gap-2 mx-auto" @click="showAddModal = true">
                            <span class="material-symbols-outlined text-xl">create_new_folder</span>
                            Add First Category
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight mb-1 sm:mb-2 text-slate-900 dark:text-white">Categories</h2>
                    <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400">Organize your bookmarks into meaningful groups for quicker access.</p>
                </div>
                
                <div class="flex-shrink-0 w-full sm:w-auto">
                    <button class="bg-primary hover:bg-primary/90 w-full sm:w-auto text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 transition-all" @click="showAddModal = true">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Category
                    </button>
                </div>
            </div>

            <!-- List View -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-visible">
                <table class="w-full text-left border-collapse min-w-full">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Slug</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Bookmarks</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-10 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="category in categories.data" :key="category.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
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
                                            <button @click="(closeDropdown(), handleInfo(category))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                <span class="material-symbols-outlined text-[16px]">info</span>
                                                Info
                                            </button>
                                            <button @click="(closeDropdown(), handleEdit(category))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                                Edit
                                            </button>
                                            <button @click="(closeDropdown(), handleDelete(category))" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors w-full text-left font-medium">
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

            <!-- Pagination -->
            <div class="mt-8 flex items-center justify-center" v-if="(categories.meta ? categories.meta.last_page : categories.last_page) > 1">
                <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                    <template v-for="(link, index) in (categories.meta ? categories.meta.links : categories.links)" :key="index">
                        <Link v-if="link.url" :href="link.url"
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 border-y border-slate-300 dark:border-slate-700',
                                link.active ? 'z-10 bg-primary text-white border-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary' : 'text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-offset-0',
                                index === 0 ? 'rounded-l-md px-2 border-l' : '',
                                index === (categories.meta ? categories.meta.links.length - 1 : categories.links.length - 1) ? 'rounded-r-md px-2 border-r' : (!link.active ? 'border-r' : '')
                            ]">
                            <span v-if="link.label.includes('Previous')" class="material-symbols-outlined text-[20px]">chevron_left</span>
                            <span v-else-if="link.label.includes('Next')" class="material-symbols-outlined text-[20px]">chevron_right</span>
                            <span v-else v-html="link.label"></span>
                        </Link>
                        <span v-else
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-400 border-y border-slate-300 dark:border-slate-700',
                                index === 0 ? 'rounded-l-md px-2 border-l' : '',
                                index === (categories.meta ? categories.meta.links.length - 1 : categories.links.length - 1) ? 'rounded-r-md px-2 border-r' : 'border-r'
                            ]">
                            <span v-if="link.label.includes('Previous')" class="material-symbols-outlined text-[20px]">chevron_left</span>
                            <span v-else-if="link.label.includes('Next')" class="material-symbols-outlined text-[20px]">chevron_right</span>
                            <span v-else v-html="link.label"></span>
                        </span>
                    </template>
                </nav>
            </div>
        </div>

        <AddCategoryModal
            :show="showAddModal"
            @close="showAddModal = false"
        />

        <InfoCategoryModal
            :show="showInfoModal"
            :category="selectedCategory"
            @close="showInfoModal = false"
        />

        <EditCategoryModal
            :show="showEditModal"
            :category="selectedCategory"
            @close="showEditModal = false"
        />

        <ConfirmDeleteCategoryModal
            :show="showDeleteModal"
            :category="selectedCategory"
            @close="showDeleteModal = false"
        />
    </DashboardLayout>
</template>
