<script setup>
import { ref, watch, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const page = usePage();
const toast = ref(null);

// Sistema de notificaciones minimalista
watch(() => page.props.message, (newMessage) => {
    if (newMessage) {
        toast.value = newMessage;
        setTimeout(() => toast.value = null, 3000);
    }
}, { immediate: true });

// Soporte para modo oscuro
const isDark = ref(false);
const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
    document.documentElement.classList.toggle('dark', isDark.value);
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }
});
</script>

<template>
    <div class="min-h-screen bg-slate-base dark:bg-midnight transition-colors duration-500 text-slate-900 dark:text-slate-100">
        <!-- Notificación Toast -->
        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="toast" class="fixed bottom-5 right-5 z-[100] max-w-sm w-full bg-white dark:bg-midnight-card/90 backdrop-blur-md shadow-2xl rounded-2xl border border-slate-200/60 dark:border-slate-700/50 p-4 flex items-center gap-3">
                <div class="bg-emerald-vibrant/10 p-2 rounded-xl">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ toast }}</p>
            </div>
        </Transition>

        <nav
            class="border-b border-slate-200/60 dark:border-slate-800/60 bg-white/70 dark:bg-midnight/70 backdrop-blur-xl sticky top-0 z-50"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex shrink-0 items-center">
                            <Link
                                :href="route('inicio')"
                                class="flex items-center gap-2 group"
                            >
                                <div
                                    class="bg-emerald-vibrant p-2 rounded-xl group-hover:scale-110 transition-all shadow-lg shadow-emerald-500/20 dark:shadow-none"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 9h18v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 2l3 7 3-7"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v12M3 15h18"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="text-xl font-black tracking-tighter text-slate-900 dark:text-white hidden md:block uppercase"
                                >
                                    Mi<span class="text-emerald-vibrant"
                                        >Despensa</span
                                    >
                                </span>
                            </Link>
                        </div>

                        <div
                            class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center"
                        >
                            <NavLink
                                :href="route('inicio')"
                                :active="route().current('inicio')"
                                class="text-sm font-black uppercase tracking-widest active:text-emerald-vibrant"
                            >
                                Inicio
                            </NavLink>

                            <NavLink
                                :href="route('alimentos.index')"
                                :active="route().current('alimentos.index')"
                                class="text-sm font-black uppercase tracking-widest active:text-emerald-vibrant"
                            >
                                Inventario
                            </NavLink>

                            <NavLink
                                :href="route('recetas.index')"
                                :active="route().current('recetas.index')"
                                class="text-sm font-black uppercase tracking-widest active:text-emerald-vibrant"
                            >
                                Mis Recetas
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center gap-4">
                        <!-- Toggle Modo Oscuro -->
                        <button 
                            @click="toggleDarkMode"
                            class="p-2 rounded-xl bg-slate-100/50 dark:bg-midnight-card/50 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-midnight-card transition-colors border border-slate-200/60 dark:border-slate-700/50"
                        >
                            <svg v-if="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </button>

                        <div class="relative">
                            <Dropdown align="right" width="48" content-classes="py-1 bg-white dark:bg-midnight-card shadow-2xl border border-slate-200/60 dark:border-slate-700/50">
                                <template #trigger>
                                    <span class="inline-flex rounded-2xl">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-2xl border border-slate-200/60 dark:border-slate-700/50 bg-slate-100/50 dark:bg-midnight-card/50 px-4 py-2.5 text-sm font-bold leading-4 text-slate-700 dark:text-slate-200 transition duration-150 ease-in-out hover:bg-slate-200/50 dark:hover:bg-midnight-card focus:outline-none focus:ring-0"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="-me-0.5 ms-2 h-4 w-4 opacity-50"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <div class="p-1 dark:bg-midnight-card">
                                        <DropdownLink
                                            :href="route('perfil.edit')"
                                            class="rounded-lg dark:text-slate-200 dark:hover:bg-slate-700"
                                        >
                                            Tu Perfil
                                        </DropdownLink>
                                        <div
                                            class="border-t border-slate-100 dark:border-slate-700 my-1"
                                        ></div>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                        >
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden gap-2">
                        <!-- Toggle Modo Oscuro Móvil -->
                        <button 
                            @click="toggleDarkMode"
                            class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            <svg v-if="!isDark" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </button>

                        <button
                            @click="
                                showingNavigationDropdown =
                                    !showingNavigationDropdown
                            "
                            class="inline-flex items-center justify-center rounded-lg p-2 text-slate-400 transition duration-150 ease-in-out hover:bg-emerald-50 dark:hover:bg-slate-800 hover:text-emerald-600 focus:outline-none"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex':
                                            !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex':
                                            showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="sm:hidden bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800"
            >
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink
                        :href="route('inicio')"
                        :active="route().current('inicio')"
                        class="dark:text-slate-300"
                    >
                        Inicio
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('alimentos.index')"
                        :active="route().current('alimentos.index')"
                        class="dark:text-slate-300"
                    >
                        Inventario
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('recetas.index')"
                        :active="route().current('recetas.index')"
                        class="dark:text-slate-300"
                    >
                        Mis Recetas
                    </ResponsiveNavLink>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 pb-1 pt-4 bg-slate-50 dark:bg-slate-900/50">
                    <div class="px-4 flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold shadow-lg shadow-emerald-500/20"
                        >
                            {{
                                $page.props.auth.user.name
                                    .charAt(0)
                                    .toUpperCase()
                            }}
                        </div>
                        <div>
                            <div class="text-base font-bold text-slate-800 dark:text-white">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1 px-2">
                        <ResponsiveNavLink
                            :href="route('perfil.edit')"
                            class="rounded-lg dark:text-slate-300"
                        >
                            Perfil
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-lg text-red-600"
                        >
                            Cerrar Sesión
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>
