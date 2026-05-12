<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-4 py-2 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-sm font-black leading-5 text-emerald-600 dark:text-emerald-400 outline-none transition duration-150 ease-in-out shadow-sm dark:shadow-none'
        : 'inline-flex items-center px-4 py-2 rounded-2xl bg-transparent text-sm font-bold leading-5 text-slate-500 dark:text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5 outline-none transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>