<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmText: {
        type: String,
        default: 'Confirmar'
    },
    cancelText: {
        type: String,
        default: 'Cancelar'
    },
    type: {
        type: String,
        default: 'danger' // 'danger', 'info', 'success'
    },
    isAlert: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'confirm']);

const close = () => {
    emit('close');
};

const confirm = () => {
    emit('confirm');
};
</script>

<template>
    <Modal :show="show" @close="close" maxWidth="sm">
        <div class="p-8 bg-white dark:bg-midnight border border-slate-200/60 dark:border-slate-800/50 rounded-[2.5rem] text-center">
            <!-- Icono según tipo -->
            <div v-if="type === 'danger'" class="w-16 h-16 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div v-else-if="type === 'success'" class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-vibrant rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div v-else class="w-16 h-16 bg-blue-50 dark:bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>

            <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2 tracking-tighter uppercase">{{ title }}</h3>
            <p class="text-slate-500 text-xs font-medium leading-relaxed mb-8">
                {{ message }}
            </p>

            <slot name="additional-content" />

            <div class="flex flex-col gap-3 mt-6">
                <DangerButton v-if="type === 'danger' && !isAlert" @click="confirm" class="w-full justify-center py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl dark:shadow-none">
                    {{ confirmText }}
                </DangerButton>
                <PrimaryButton v-else-if="!isAlert" @click="confirm" class="w-full justify-center py-4 bg-slate-900 dark:bg-emerald-vibrant text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200/50 dark:shadow-none">
                    {{ confirmText }}
                </PrimaryButton>
                
                <!-- Si es solo alerta, mostramos el botón de cerrar como primario -->
                <PrimaryButton v-if="isAlert" @click="close" class="w-full justify-center py-4 bg-slate-900 dark:bg-emerald-vibrant text-white rounded-2xl font-black text-[10px] uppercase tracking-widest">
                    Cerrar
                </PrimaryButton>
                
                <SecondaryButton v-if="!isAlert" @click="close" class="w-full justify-center py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest">
                    {{ cancelText }}
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
