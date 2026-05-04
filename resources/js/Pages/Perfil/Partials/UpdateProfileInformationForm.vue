<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                Información de la Cuenta
            </h2>

            <p class="mt-3 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em]">
                Actualiza los datos básicos de tu perfil y acceso.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('perfil.update'))"
            class="mt-12 space-y-10"
        >
            <div>
                <InputLabel for="name" value="Nombre Completo" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full p-5 text-lg font-bold"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Correo Electrónico" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full p-5 text-lg font-bold"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-6 text-sm text-slate-800 dark:text-slate-300 font-bold italic">
                    Tu dirección de correo no ha sido verificada.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="text-emerald-500 hover:text-emerald-600 font-black uppercase tracking-widest text-[10px] ml-2 underline"
                    >
                        Reenviar verificación
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-xs font-black text-emerald-500 uppercase tracking-widest"
                >
                    Se ha enviado un nuevo enlace a tu correo.
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <PrimaryButton :disabled="form.processing" class="rounded-2xl px-12 py-6 bg-emerald-500 font-black text-[11px] uppercase tracking-widest shadow-2xl shadow-emerald-100 dark:shadow-none transition-all hover:scale-105 active:scale-95">Guardar Cambios</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-[11px] font-black text-emerald-500 uppercase tracking-[0.2em]"
                    >
                        // Datos guardados correctamente.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
