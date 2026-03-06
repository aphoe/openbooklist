<script setup>
import { useForm } from '@inertiajs/vue3';
import ModalActionButtons from '@/Components/Forms/ModalActionButtons.vue';

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

const form = useForm({});

const deleteTag = () => {
    if (!props.tag) return;

    form.delete(route('tags.destroy', props.tag.id), {
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="show" class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

                <div
                    class="relative z-10 w-full max-w-sm bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800">
                    <div class="p-6">
                        <div
                            class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-500 mb-4">
                            <span class="material-symbols-outlined text-2xl">warning</span>
                        </div>

                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Delete Tag</h2>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-6">
                            Are you sure you want to delete <span
                                class="font-semibold text-slate-900 dark:text-white">"{{ tag?.name }}"</span>? This
                            action cannot be undone. Bookmarks using this tag will no longer be associated with it.
                        </p>

                        <div class="flex gap-3 w-full">
                            <button type="button"
                                class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-semibold transition-colors border border-slate-200 dark:border-slate-700"
                                @click="$emit('close')" :disabled="form.processing">
                                Cancel
                            </button>
                            <button type="button"
                                class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                @click="deleteTag" :disabled="form.processing">
                                <span v-if="form.processing"
                                    class="material-symbols-outlined text-sm animate-spin">refresh</span>
                                <span v-else class="material-symbols-outlined text-[18px]">delete</span>
                                Delete Tag
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
