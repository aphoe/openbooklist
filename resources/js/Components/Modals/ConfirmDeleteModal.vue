<script setup>
import { useForm } from '@inertiajs/vue3';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

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

const form = useForm({});

const submit = () => {
    if (!props.bookmark) return;
    
    // Assuming a generic delete route exists. Adjust as necessary if it doesn't.
    form.delete(route('bookmarks.destroy', props.bookmark.id), {
        onSuccess: () => {
            emit('close');
        },
    });
};

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
            <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4 px-4 sm:p-0" @click.self="close">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="close"></div>

                <!-- Modal -->
                <div class="relative z-10 w-full max-w-md bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden text-left align-middle transition-all transform sm:my-8 sm:w-full">
                    <div class="bg-white dark:bg-slate-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="material-symbols-outlined text-red-600">warning</span>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-white" id="modal-title">Delete Bookmark</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        Are you sure you want to delete <span class="font-semibold text-slate-700 dark:text-slate-300">"{{ bookmark?.title }}"</span>? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm" @click="submit" :disabled="form.processing">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Delete
                        </button>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-base font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="close" ref="cancelButtonRef">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
