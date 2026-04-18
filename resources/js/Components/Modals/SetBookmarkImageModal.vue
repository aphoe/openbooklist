<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalActionButtons from '@/Components/Forms/ModalActionButtons.vue';
import TextInput from '@/Components/Forms/TextInput.vue';

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

const form = useForm({
    image_source: 'url',
    image_url: '',
});

watch(() => props.show, (isOpen) => {
    if (!isOpen) {
        return;
    }

    form.reset();
    form.clearErrors();
});

watch(() => form.image_source, (imageSource) => {
    if (imageSource === 'screenshot') {
        form.image_url = '';
    }
});

const submit = () => {
    if (!props.bookmark) {
        return;
    }

    form.post(route('bookmarks.set-image', props.bookmark.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    emit('close');
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
                    class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Set Bookmark Image</h2>
                        <button type="button"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                            @click="close">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Image Source <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <label class="flex items-start gap-2 rounded-lg border border-slate-200 dark:border-slate-700 p-3 cursor-pointer">
                                    <input v-model="form.image_source" type="radio" value="url" class="mt-0.5" />
                                    <span class="text-sm text-slate-700 dark:text-slate-300">
                                        <span class="font-medium">Image URL</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400">Upload from a direct image link</span>
                                    </span>
                                </label>
                                <label class="flex items-start gap-2 rounded-lg border border-slate-200 dark:border-slate-700 p-3 cursor-pointer">
                                    <input v-model="form.image_source" type="radio" value="screenshot" class="mt-0.5" />
                                    <span class="text-sm text-slate-700 dark:text-slate-300">
                                        <span class="font-medium">Website Screenshot</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400">Capture the bookmark page automatically</span>
                                    </span>
                                </label>
                            </div>
                            <p v-if="form.errors.image_source" class="mt-1 text-sm text-red-500">{{ form.errors.image_source }}</p>
                        </div>

                        <div v-if="form.image_source === 'url'">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Image URL <span class="text-red-500">*</span>
                            </label>
                            <TextInput v-model="form.image_url" type="url" placeholder="https://example.com/image.jpg" />
                            <p v-if="form.errors.image_url" class="mt-1 text-sm text-red-500">{{ form.errors.image_url }}</p>
                        </div>

                        <p v-else class="text-xs text-slate-500 dark:text-slate-400">
                            We will capture a 512x269 JPEG screenshot of <span class="font-medium">{{ bookmark?.url }}</span>.
                        </p>

                        <ModalActionButtons
                            :processing="form.processing"
                            submit-text="Update Image"
                            submit-icon="image"
                            @cancel="close"
                        />
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

