<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-midnight-card px-4 py-2 text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-300 shadow-sm transition duration-150 ease-in-out hover:bg-slate-50 dark:hover:bg-midnight focus:outline-none focus:ring-2 focus:ring-emerald-vibrant/20 focus:ring-offset-2 dark:focus:ring-offset-midnight disabled:opacity-25"
    >
        <slot />
    </button>
</template>
