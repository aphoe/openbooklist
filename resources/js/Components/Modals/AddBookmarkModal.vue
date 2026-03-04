<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import TextInput from '@/Components/Forms/TextInput.vue';
import SearchableDropdown from '@/Components/Forms/SearchableDropdown.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    tags: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    url: '',
    title: '',
    category_id: null,
    tags: [],
    description: '',
    image: '',
});

const isFetchingMetadata = ref(false);
const imagePreview = ref(null);

const fetchMetadata = async () => {
    if (!form.url || isFetchingMetadata.value) {
        return;
    }

    try {
        new URL(form.url);
    } catch {
        return;
    }

    isFetchingMetadata.value = true;

    try {
        const { data: result } = await axios.post(route('bookmarks.fetch-metadata'), {
            url: form.url,
        });

        if (result.success && result.data) {
            if (result.data.title && !form.title) {
                form.title = result.data.title;
            }
            if (result.data.description && !form.description) {
                form.description = result.data.description;
            }
            if (result.data.image && !form.image) {
                form.image = result.data.image;
                imagePreview.value = result.data.image;
            }
        }
    } catch (error) {
        console.error('Failed to fetch metadata:', error);
    } finally {
        isFetchingMetadata.value = false;
    }
};

const toggleTag = (tagId) => {
    const index = form.tags.indexOf(tagId);
    if (index === -1) {
        form.tags.push(tagId);
    } else {
        form.tags.splice(index, 1);
    }
};

const isTagSelected = (tagId) => {
    return form.tags.includes(tagId);
};

const submit = () => {
    form.post(route('bookmarks.store'), {
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            emit('close');
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    imagePreview.value = null;
    emit('close');
};

watch(() => form.image, (newVal) => {
    imagePreview.value = newVal || null;
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
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Add Bookmark</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" @click="close">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <form @submit.prevent="submit" class="p-5 space-y-4">
                        <!-- URL -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">URL <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <TextInput
                                    v-model="form.url"
                                    type="url"
                                    placeholder="https://example.com/article"
                                    @blur="fetchMetadata"
                                />
                                <div v-if="isFetchingMetadata" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p v-if="form.errors.url" class="mt-1 text-sm text-red-500">{{ form.errors.url }}</p>
                        </div>

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Title <span class="text-red-500">*</span></label>
                            <TextInput v-model="form.title" placeholder="Page title" />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Category</label>
                            <SearchableDropdown
                                v-model="form.category_id"
                                :options="categories"
                                placeholder="Select category..."
                                search-placeholder="Search categories..."
                            />
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-500">{{ form.errors.category_id }}</p>
                        </div>

                        <!-- Tags -->
                        <div v-if="tags.length > 0">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Tags</label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="tag in tags"
                                    :key="tag.id"
                                    type="button"
                                    class="px-3 py-1 rounded-full text-sm font-medium transition-all border"
                                    :class="isTagSelected(tag.id)
                                        ? 'bg-primary/10 text-primary border-primary/30'
                                        : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:border-primary/30'"
                                    @click="toggleTag(tag.id)"
                                >
                                    {{ tag.name }}
                                </button>
                            </div>
                            <p v-if="form.errors.tags" class="mt-1 text-sm text-red-500">{{ form.errors.tags }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Description</label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Brief description..."
                                class="form-input block w-full rounded-lg border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary px-4 py-3 text-base transition-colors resize-none"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                        </div>

                        <!-- Image -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Image</label>
                            <TextInput v-model="form.image" placeholder="Image URL" />
                            <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
                            <!-- Image Preview -->
                            <div v-if="imagePreview" class="mt-2 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                                <img :src="imagePreview" alt="Preview" class="w-full h-32 object-cover" @error="imagePreview = null" />
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="pt-2">
                            <SubmitButton :processing="form.processing">
                                <span class="material-symbols-outlined text-lg mr-2">bookmark_add</span>
                                Save Bookmark
                            </SubmitButton>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
