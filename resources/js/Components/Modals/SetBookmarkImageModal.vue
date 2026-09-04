<script setup>
import { ref, watch, onUnmounted } from 'vue';
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

const MAX_UPLOAD_BYTES = 10 * 1024 * 1024;

const form = useForm({
    image_source: 'url',
    image_url: '',
    image_file: null,
});

const fileInput = ref(null);
const filePreview = ref(null);
const isDragging = ref(false);

const revokePreview = () => {
    if (filePreview.value) {
        URL.revokeObjectURL(filePreview.value);
        filePreview.value = null;
    }
};

const clearFile = () => {
    revokePreview();
    form.image_file = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const setFile = (file) => {
    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        form.setError('image_file', 'Please choose an image file.');
        return;
    }

    if (file.size > MAX_UPLOAD_BYTES) {
        form.setError('image_file', 'Image must be 10MB or smaller.');
        return;
    }

    revokePreview();
    form.image_file = file;
    filePreview.value = URL.createObjectURL(file);
    form.clearErrors('image_file');

    if (form.image_source !== 'upload') {
        form.image_source = 'upload';
    }
};

const onFilePick = (event) => {
    setFile(event.target.files?.[0] ?? null);
};

const onDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    setFile(event.dataTransfer?.files?.[0] ?? null);
};

const handlePaste = (event) => {
    const items = event.clipboardData?.items;
    if (!items) {
        return;
    }

    for (const item of items) {
        if (item.kind === 'file' && item.type.startsWith('image/')) {
            const file = item.getAsFile();
            if (file) {
                event.preventDefault();
                setFile(file);
            }
            return;
        }
    }
};

watch(() => props.show, (isOpen) => {
    if (isOpen) {
        form.reset();
        form.clearErrors();
        clearFile();
        document.addEventListener('paste', handlePaste);
    } else {
        document.removeEventListener('paste', handlePaste);
        clearFile();
    }
});

watch(() => form.image_source, (imageSource) => {
    if (imageSource !== 'url') {
        form.image_url = '';
    }
    if (imageSource !== 'upload') {
        clearFile();
        form.clearErrors('image_file');
    }
});

onUnmounted(() => {
    document.removeEventListener('paste', handlePaste);
    revokePreview();
});

const submit = () => {
    if (!props.bookmark) {
        return;
    }

    form.post(route('bookmarks.set-image', props.bookmark.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            clearFile();
            emit('close');
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    clearFile();
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
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <label class="flex items-start gap-2 rounded-lg border border-slate-200 dark:border-slate-700 p-3 cursor-pointer">
                                    <input v-model="form.image_source" type="radio" value="url" class="mt-0.5" />
                                    <span class="text-sm text-slate-700 dark:text-slate-300">
                                        <span class="font-medium">Image URL</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400">Use a direct image link</span>
                                    </span>
                                </label>
                                <label class="flex items-start gap-2 rounded-lg border border-slate-200 dark:border-slate-700 p-3 cursor-pointer">
                                    <input v-model="form.image_source" type="radio" value="upload" class="mt-0.5" />
                                    <span class="text-sm text-slate-700 dark:text-slate-300">
                                        <span class="font-medium">Upload</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400">From your device or paste</span>
                                    </span>
                                </label>
                                <label class="flex items-start gap-2 rounded-lg border border-slate-200 dark:border-slate-700 p-3 cursor-pointer">
                                    <input v-model="form.image_source" type="radio" value="screenshot" class="mt-0.5" />
                                    <span class="text-sm text-slate-700 dark:text-slate-300">
                                        <span class="font-medium">Screenshot</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400">Capture the page automatically</span>
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

                        <div v-else-if="form.image_source === 'upload'">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Upload image <span class="text-red-500">*</span>
                            </label>

                            <div v-if="filePreview" class="relative overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
                                <img :src="filePreview" alt="Selected image preview" class="w-full h-40 object-contain bg-slate-50 dark:bg-slate-800" />
                                <button type="button" @click="clearFile"
                                    class="absolute top-2 right-2 inline-flex items-center gap-1 rounded-md bg-black/60 px-2 py-1 text-xs font-medium text-white hover:bg-black/80 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Remove
                                </button>
                            </div>

                            <label v-else
                                :class="[
                                    'flex flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed px-4 py-8 text-center cursor-pointer transition-colors',
                                    isDragging
                                        ? 'border-primary bg-primary/5'
                                        : 'border-slate-300 dark:border-slate-600 hover:border-primary hover:bg-slate-50 dark:hover:bg-slate-800/60'
                                ]"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop="onDrop">
                                <span class="material-symbols-outlined text-3xl text-slate-400">add_photo_alternate</span>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Click to browse, drag &amp; drop, or paste
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">PNG, JPG, GIF or WebP &middot; up to 10MB</span>
                                <input ref="fileInput" type="file" class="hidden"
                                    accept="image/png,image/jpeg,image/gif,image/webp" @change="onFilePick" />
                            </label>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                The image is resized to fit within 512&times;512px.
                            </p>
                            <p v-if="form.errors.image_file" class="mt-1 text-sm text-red-500">{{ form.errors.image_file }}</p>
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
