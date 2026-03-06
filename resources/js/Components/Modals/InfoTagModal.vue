<script setup>
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    tag: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="show" class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

                <div
                    class="relative z-10 w-full max-w-md bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tag Info</h2>
                        <button type="button"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                            @click="$emit('close')">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="p-5" v-if="tag">
                        <div
                            class="mb-6 flex flex-col items-center justify-center p-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <div
                                class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-3 shadow-inner">
                                <span class="material-symbols-outlined text-[32px]">loyalty</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ tag.name }}</h3>
                            <code
                                class="text-xs bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-1 rounded mt-2">{{ tag.slug }}</code>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h4
                                    class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                                    Description</h4>
                                <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed">{{ tag.description
                                    || 'No description provided.' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <h4
                                        class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                                        Usage</h4>
                                    <p
                                        class="text-slate-900 dark:text-white font-medium text-sm flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-primary text-[16px]">bookmark</span>
                                        {{ tag.bookmarks_count || 0 }} bookmarks
                                    </p>
                                </div>
                                <div>
                                    <h4
                                        class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                                        Created</h4>
                                    <p class="text-slate-900 dark:text-white font-medium text-sm">
                                        {{ new Date(tag.created_at).toLocaleDateString() }}
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
