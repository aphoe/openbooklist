<script setup>
import { computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    bookmark: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};

const formattedDate = computed(() => {
    if (!props.bookmark || !props.bookmark.created_at) return '';
    return new Date(props.bookmark.created_at).toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4" @click.self="close">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="close"></div>

                <!-- Modal -->
                <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[calc(100vh-8rem)] overflow-y-auto">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white truncate pr-4">{{ bookmark?.title || 'Bookmark Info' }}</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors flex-shrink-0" @click="close">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-5 space-y-6" v-if="bookmark">
                        <!-- Image -->
                        <div v-if="bookmark.image_url" class="rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                            <img :src="bookmark.image_url" :alt="bookmark.title" class="w-full h-48 object-cover" />
                        </div>

                        <!-- Details Grid -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">URL</h3>
                                <a :href="bookmark.url" target="_blank" class="text-sm font-medium text-primary hover:underline break-all">
                                    {{ bookmark.url }}
                                </a>
                            </div>

                            <div v-if="bookmark.description">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Description</h3>
                                <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ bookmark.description }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Domain</h3>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ bookmark.domain || '-' }}</p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Date Added</h3>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ formattedDate }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Category</h3>
                                <span v-if="bookmark.category" class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ bookmark.category.name }}
                                </span>
                                <span v-else class="text-sm text-slate-500 italic">Uncategorized</span>
                            </div>

                            <div v-if="bookmark.tags && bookmark.tags.length > 0">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="tag in bookmark.tags" :key="tag.id" class="inline-flex items-center px-2 py-1 rounded bg-primary/10 text-primary text-xs font-medium">
                                        {{ tag.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                        <a :href="bookmark.url" target="_blank" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                            Visit
                        </a>
                        <button type="button" @click="close" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
