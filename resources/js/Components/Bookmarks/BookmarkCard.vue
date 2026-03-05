<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    bookmark: {
        type: Object,
        required: true
    },
});

const emit = defineEmits(['edit', 'delete', 'info']);

const showMenu = ref(false);
const toggleMenu = () => { showMenu.value = !showMenu.value; };
const closeMenu = () => { showMenu.value = false; };

const clickOutside = (e) => {
    if (showMenu.value && !e.target.closest('.bookmark-menu-container')) {
        closeMenu();
    }
};

onMounted(() => document.addEventListener('click', clickOutside));
onUnmounted(() => document.removeEventListener('click', clickOutside));
</script>

<template>
    <div class="group bg-white dark:bg-[#1e293b] rounded-xl overflow-visible border border-slate-200 dark:border-slate-700 shadow-none hover:shadow-sm hover:border-primary/50 dark:hover:border-primary/50 transition-all duration-200 flex flex-col relative z-0 hover:z-10">
        <!-- Floating Menu (Moved outside overflow-hidden) -->
        <div class="absolute top-2 right-2 bookmark-menu-container z-20">
            <button @click.stop="toggleMenu" class="p-1.5 bg-white/90 dark:bg-black/50 backdrop-blur-sm rounded-full opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity text-slate-700 dark:text-white hover:text-primary outline-none">
                <span class="material-symbols-outlined text-[18px] block">more_horiz</span>
            </button>
            
            <!-- Dropdown Menu -->
            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                <div v-if="showMenu" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-md shadow-lg border border-slate-200 dark:border-slate-700 z-50 py-1 origin-top-right">
                    <a :href="bookmark.url" target="_blank" @click="closeMenu" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                        <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        Visit
                    </a>
                    <button @click.stop="(closeMenu(), emit('info', bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                        Info
                    </button>
                    <button @click.stop="(closeMenu(), emit('edit', bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        Edit
                    </button>
                    <button @click.stop="(closeMenu(), emit('delete', bookmark))" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors w-full text-left font-medium">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        Delete
                    </button>
                </div>
            </transition>
        </div>

        <div class="relative aspect-video w-full overflow-hidden rounded-t-xl bg-slate-100 dark:bg-slate-800">
            <div class="w-full h-full bg-center bg-cover transform group-hover:scale-105 transition-transform duration-500"
                :style="`background-image: url('${bookmark.image_url}');`">
            </div>
        </div>
        <div class="p-4 flex flex-col flex-1 bg-white dark:bg-[#1e293b] rounded-b-xl z-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <h3 class="text-slate-900 dark:text-slate-100 text-base font-semibold leading-snug line-clamp-2 group-hover:text-primary transition-colors flex items-center gap-1 w-full">
                    <span class="truncate">{{ bookmark.title }}</span>
                    <a :href="bookmark.url" target="_blank" class="text-slate-400 hover:text-primary shrink-0 ml-auto" title="Visit">
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    </a>
                </h3>
            </div>
            <a :href="bookmark.url" target="_blank" class="text-slate-500 dark:text-slate-400 text-xs font-medium mb-3 hover:underline truncate block">
                {{ bookmark.domain || bookmark.url }}
            </a>
            <div class="mt-auto flex flex-wrap gap-2 pt-2">
                <span v-for="tag in bookmark.tags" :key="tag.id" class="inline-flex items-center px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-xs font-medium text-slate-600 dark:text-slate-300">
                    {{ tag.name }}
                </span>
            </div>
        </div>
    </div>
</template>
