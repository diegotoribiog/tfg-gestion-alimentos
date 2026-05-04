<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('perfil.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-10">
        <header>
            <h2 class="text-2xl font-black text-rose-600 dark:text-rose-400 uppercase tracking-tight">
                Zona de Peligro
            </h2>

            <p class="mt-3 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em] leading-relaxed">
                Al eliminar tu cuenta, todos tus registros (alimentos, inventario y datos) se perderán para siempre.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" class="rounded-2xl px-10 py-5 bg-rose-500 font-black text-[11px] uppercase tracking-widest shadow-2xl shadow-rose-100 dark:shadow-none transition-all hover:scale-105 active:scale-95">Eliminar Cuenta</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-16 dark:bg-slate-900 rounded-[4rem]">
                <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">
                    ¿Confirmar eliminación?
                </h2>

                <p class="mt-6 text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-relaxed">
                    Esta acción es irreversible. Para continuar, introduce tu contraseña actual.
                </p>

                <div class="mt-12">
                    <InputLabel
                        for="password"
                        value="Contraseña de Seguridad"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full p-6 text-lg font-bold"
                        placeholder="Contraseña"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-4" />
                </div>

                <div class="mt-12 flex justify-end gap-6">
                    <SecondaryButton @click="closeModal" class="rounded-[1.5rem] px-10 py-5 border-none bg-slate-100 dark:bg-slate-800 font-black text-[11px] uppercase tracking-widest">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="rounded-[1.5rem] px-10 py-5 bg-rose-600 text-white font-black text-[11px] uppercase tracking-widest transition-all hover:scale-110 active:scale-95 shadow-xl"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Confirmar Baja
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
