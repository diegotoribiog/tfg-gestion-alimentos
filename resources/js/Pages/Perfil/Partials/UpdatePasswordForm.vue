<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                Seguridad de Acceso
            </h2>

            <p class="mt-3 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em]">
                Cambia tu contraseña periódicamente para mantener tu cuenta segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-12 space-y-10">
            <div>
                <InputLabel for="current_password" value="Contraseña Actual" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full p-5 text-lg font-bold"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="Nueva Contraseña" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full p-5 text-lg font-bold"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Nueva Contraseña"
                    class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full p-5 text-lg font-bold"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-6 pt-4">
                <PrimaryButton :disabled="form.processing" class="rounded-2xl px-12 py-6 bg-emerald-500 font-black text-[11px] uppercase tracking-widest shadow-2xl shadow-emerald-100 dark:shadow-none transition-all hover:scale-105 active:scale-95">Actualizar Seguridad</PrimaryButton>

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
                        // Seguridad actualizada.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
