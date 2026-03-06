<script setup>
import { computed } from 'vue';

const props = defineProps({
    tag: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['info', 'edit', 'delete']);

const sizeClasses = computed(() => {
    const count = props.tag.bookmarks_count || 0;

    if (count >= 100) {
        // Huge Tag
        return {
            button: 'rounded-xl px-6 py-4',
            text: 'text-2xl',
            badge: 'bg-primary/10 text-primary px-2 py-1',
            badgeText: 'text-xs'
        };
    } else if (count >= 50) {
        // Large Tag
        return {
            button: 'rounded-lg px-5 py-3',
            text: 'text-lg',
            badge: 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-2 py-0.5',
            badgeText: 'text-xs'
        };
    } else if (count >= 20) {
        // Medium Tag
        return {
            button: 'rounded-lg px-4 py-2',
            text: 'text-base',
            badge: 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-2 py-0.5',
            badgeText: 'text-xs'
        };
    } else {
        // Small Tag
        return {
            button: 'rounded px-3 py-1.5',
            text: 'text-sm',
            badge: 'text-slate-400 dark:text-slate-500',
            badgeText: 'text-[10px]'
        };
    }
});
</script>

<template>
    <div class="group relative inline-flex">
        <button
            class="bg-white dark:bg-slate-800 hover:bg-primary/10 dark:hover:bg-primary/20 hover:border-primary border border-slate-200 dark:border-slate-700 flex items-center gap-3 transition-all duration-200 shadow-sm"
            :class="sizeClasses.button" @click="$emit('info', tag)" title="Click for tag info">
            <span class="font-bold text-slate-800 dark:text-slate-100" :class="sizeClasses.text">
                {{ tag.name }}
            </span>
            <span class="font-bold rounded-full flex-shrink-0" :class="[sizeClasses.badge, sizeClasses.badgeText]">
                {{ tag.bookmarks_count || 0 }}
            </span>
        </button>
        <!-- Hover Actions -->
        <div
            class="absolute -top-3 -right-3 hidden group-hover:flex gap-1 bg-white dark:bg-slate-800 p-1 rounded-lg shadow-lg border border-slate-100 dark:border-slate-700 z-10 animate-fade-in shadow-xl">
            <button @click.stop="$emit('info', tag)"
                class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded text-slate-500 hover:text-primary transition-colors"
                title="Info">
                <span class="material-symbols-outlined text-sm">info</span>
            </button>
            <button @click.stop="$emit('edit', tag)"
                class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded text-slate-500 hover:text-primary transition-colors"
                title="Edit">
                <span class="material-symbols-outlined text-sm">edit</span>
            </button>
            <button @click.stop="$emit('delete', tag)"
                class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-slate-500 hover:text-red-500 transition-colors"
                title="Delete">
                <span class="material-symbols-outlined text-sm">delete</span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
