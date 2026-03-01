<script setup>
</script>

<template>
    <div class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center p-4 flex-col">
        <div class="w-full max-w-[480px] mb-4" v-if="$page.props.status || $page.props.error">
            <div v-if="$page.props.status" class="font-medium text-sm text-green-600 dark:text-green-400 text-center bg-green-50 dark:bg-green-900/30 p-4 rounded-xl border border-green-200 dark:border-green-800 shadow-sm">
                {{ $page.props.status }}
            </div>
            <div v-if="$page.props.error" class="font-medium text-sm text-red-600 dark:text-red-400 text-center bg-red-50 dark:bg-red-900/30 p-4 rounded-xl border border-red-200 dark:border-red-800 shadow-sm mt-3">
                {{ $page.props.error }}
            </div>
        </div>

        <!-- Login Card Container -->
        <div class="w-full max-w-[480px] bg-white dark:bg-[#1a2332] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <slot />
        </div>
    </div>
</template>
