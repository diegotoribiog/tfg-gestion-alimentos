<script setup>
import { Head, useForm } from '@inertiajs/vue3';
// Importamos los componentes esenciales para mantener la lógica de Breeze
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Props que Laravel envía automáticamente a través del enlace de recuperación
const props = defineProps({
    email: String,
    token: String,
});

// Inicialización del formulario con los datos necesarios para resetear la clave
const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

// Función de envío: Laravel procesará el token y actualizará la contraseña en la BD
const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Restablecer contraseña" />

    <div class="relative flex min-h-screen bg-white">
        
        <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden items-center justify-center">
            <img 
                src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                alt="Despensa organizada" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            
            <div class="relative z-10 flex flex-col items-center text-center px-12 text-white">
                <h1 class="text-7xl font-extrabold tracking-tighter mb-6">
                    Nueva<br/><span class="text-emerald-400">Clave</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-md leading-relaxed">
                    Estás a un paso de recuperar el acceso total a tu inventario.
                    Crea una contraseña segura que no olvides esta vez.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 bg-gray-50 py-12">
            <div class="max-w-md w-full mx-auto">
                
                <div class="lg:hidden mb-12 text-center">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mi<span class="text-emerald-500">Despensa</span></h2>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Restablecer contraseña</h2>
                    <p class="text-gray-500 mt-2 font-medium italic">
                        Por favor, introduce tu nueva clave de acceso.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Correo electrónico" class="text-gray-600 ml-1" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full bg-gray-100 border-gray-200 rounded-xl py-3 text-gray-500 cursor-not-allowed"
                            v-model="form.email"
                            required
                            readonly
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Nueva contraseña" class="text-gray-600 ml-1" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.password"
                            required
                            autofocus
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Repetir nueva contraseña" class="text-gray-600 ml-1" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Confirma tu clave"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="pt-2">
                        <PrimaryButton
                            class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-bold transition-all transform active:scale-[0.98] shadow-lg shadow-emerald-200 uppercase tracking-widest"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            ACTUALIZAR CONTRASEÑA
                        </PrimaryButton>
                    </div>
                </form>

                <div class="mt-16 text-center text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                    © 2026 MiDespensa · Seguridad de Cuentas
                </div>
            </div>
        </div>
    </div>
</template>