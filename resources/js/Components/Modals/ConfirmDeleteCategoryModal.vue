<script setup>
import { useForm } from '@inertiajs/vue3';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

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

const form = useForm({});

const submit = () => {
    form.delete(route('categories.destroy', props.category?.id), {
        onSuccess: () => emit('close'),
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
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="close">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="close"></div>
                
                <div class="relative z-10 w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4 text-red-600 dark:text-red-400">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Delete Category?</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                            Are you sure you want to delete the category <span class="font-semibold text-slate-700 dark:text-slate-300">"{{ category?.name }}"</span>? This action cannot be undone. Bookmarks in this category will become uncategorized.
                        </p>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex gap-3 justify-end">
                        <button 
                            type="button" 
                            class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors border border-slate-300 dark:border-slate-600"
                            @click="close"
                            :disabled="form.processing"
                        >
                            Cancel
                        </button>
                        
                        <form @submit.prevent="submit">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Delete Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
