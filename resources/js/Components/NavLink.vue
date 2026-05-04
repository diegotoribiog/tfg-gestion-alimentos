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
        ? 'inline-flex items-center px-4 py-2 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-sm font-black leading-5 text-emerald-600 dark:text-emerald-400 outline-none transition duration-150 ease-in-out shadow-sm shadow-emerald-100 dark:shadow-none'
        : 'inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold leading-5 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 outline-none transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>