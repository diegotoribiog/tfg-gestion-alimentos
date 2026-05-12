<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 dark:bg-midnight pt-6 sm:justify-center sm:pt-0 transition-colors duration-500"
    >
        <!-- Botón de Modo Oscuro -->
        <div class="fixed top-5 right-5 z-50">
            <ThemeToggle />
        </div>

        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500 dark:text-gray-400" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white dark:bg-midnight-card px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg border border-transparent dark:border-slate-800/50"
        >
            <slot />
        </div>
    </div>
</template>
