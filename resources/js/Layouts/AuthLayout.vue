<script setup>
</script>

<template>
    <div class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">
        <!-- Login Card Container -->
        <div class="w-full max-w-[480px] bg-white dark:bg-[#1a2332] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <slot />
        </div>
    </div>
</template>
