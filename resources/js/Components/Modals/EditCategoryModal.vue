<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/Forms/TextInput.vue';
import ModalActionButtons from '@/Components/Forms/ModalActionButtons.vue';

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

const form = useForm({
    name: '',
    description: '',
});

watch(() => props.category, (newVal) => {
    if (newVal) {
        form.name = newVal.name;
        form.description = newVal.description || '';
    }
}, { deep: true, immediate: true });

const submit = () => {
    form.put(route('categories.update', props.category.id), {
        onSuccess: () => {
            emit('close');
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    if (props.category) {
        form.name = props.category.name;
        form.description = props.category.description || '';
    }
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
            <div v-if="show" class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
                
                <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[calc(100vh-8rem)] overflow-y-auto">
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Edit Category</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" @click="close">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Name <span class="text-red-500">*</span></label>
                            <TextInput v-model="form.name" placeholder="Category Name" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>

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

                        <ModalActionButtons 
                            :processing="form.processing" 
                            submit-text="Save Changes" 
                            submit-icon="save" 
                            @cancel="close" 
                        />
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
