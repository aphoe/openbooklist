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
    bookmark: {
        type: Object,
        default: null,
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
});

watch(() => props.bookmark, (newVal) => {
    if (newVal) {
        form.url = newVal.url || '';
        form.title = newVal.title || '';
        form.category_id = newVal.category_id || null;
        form.tags = newVal.tags ? newVal.tags.map(t => t.id) : [];
        form.description = newVal.description || '';
    }
}, { immediate: true });

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
    // Assuming a generic update route exists. Adjust as necessary if it doesn't.
    form.put(route('bookmarks.update', props.bookmark.id), {
        onSuccess: () => {
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
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Edit Bookmark</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" @click="close">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <form @submit.prevent="submit" class="p-5 space-y-4">
                        <!-- URL -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">URL <span class="text-red-500">*</span></label>
                            <TextInput
                                v-model="form.url"
                                type="url"
                                placeholder="https://example.com/article"
                            />
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

                        <!-- Submit -->
                        <div class="pt-2 flex gap-3">
                            <button type="button" @click="close" class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors hidden sm:block">
                                Cancel
                            </button>
                            <SubmitButton class="flex-1" :processing="form.processing">
                                <span class="material-symbols-outlined text-lg mr-2">save</span>
                                Save Changes
                            </SubmitButton>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
