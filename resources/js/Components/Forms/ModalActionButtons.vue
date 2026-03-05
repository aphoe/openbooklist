<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

defineProps({
    processing: {
        type: Boolean,
        default: false,
    },
    submitText: {
        type: String,
        default: 'Save',
    },
    submitIcon: {
        type: String,
        default: null,
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
});

defineEmits(['cancel']);
</script>

<template>
    <div class="pt-2 flex gap-3">
        <button type="button" @click="$emit('cancel')" class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors hidden sm:block">
            {{ cancelText }}
        </button>
        <SubmitButton class="flex-1" :processing="processing">
            <span v-if="submitIcon" class="material-symbols-outlined text-lg mr-2">{{ submitIcon }}</span>
            {{ submitText }}
        </SubmitButton>
    </div>
</template>
