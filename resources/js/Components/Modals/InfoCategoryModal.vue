<script setup>
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    category: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};
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
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="close"></div>
                
                <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[calc(100vh-8rem)] overflow-y-auto">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Category Info</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" @click="close">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div v-if="category" class="p-5 space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-1 min-w-0 space-y-1">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white break-words">{{ category.name }}</h3>
                                <p class="text-sm font-mono text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-400 px-2 py-0.5 rounded inline-block">{{ category.slug }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Description -->
                            <div>
                                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Description</h4>
                                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed border border-slate-100 dark:border-slate-800 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                                    {{ category.description || 'No description provided.' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bookmarks Count</h4>
                                    <p class="text-sm text-slate-900 dark:text-white font-medium">
                                        {{ category.bookmarks_count || 0 }} bookmarks
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Date Created</h4>
                                    <p class="text-sm text-slate-900 dark:text-white font-medium">
                                        {{ new Date(category.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
