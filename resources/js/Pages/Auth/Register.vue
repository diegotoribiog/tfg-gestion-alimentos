<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Aunque no lo usemos como wrapper, se importa por defecto
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Crear cuenta - MiDespensa" />

    <div class="relative flex min-h-screen bg-white">
        
        <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden items-center justify-center">
            <img 
                src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                alt="Despensa organizada" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            
            <div class="relative z-10 flex flex-col items-center text-center px-12 text-white">
                <h1 class="text-7xl font-extrabold tracking-tighter mb-6">
                    Únete a <br/> Mi<span class="text-emerald-400">Despensa</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-md leading-relaxed">
                    Empieza hoy mismo a organizar tu cocina. 
                    Es gratis, rápido y para siempre.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 bg-gray-50 py-12">
            <div class="max-w-md w-full mx-auto">
                
                <div class="lg:hidden mb-10 text-center">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mi<span class="text-emerald-500">Despensa</span></h2>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Crear cuenta</h2>
                    <p class="text-gray-500 mt-3 font-medium">
                        ¿Ya eres miembro? 
                        <Link :href="route('login')" class="text-emerald-600 font-bold hover:text-emerald-700 transition decoration-2 underline-offset-4">Inicia sesión</Link>
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <InputLabel for="name" value="Nombre completo" class="text-gray-600 ml-1" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Escribe tu nombre"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Correo electrónico" class="text-gray-600 ml-1" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="email@ejemplo.com"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Contraseña" class="text-gray-600 ml-1" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirmar contraseña" class="text-gray-600 ml-1" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repite tu contraseña"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="pt-4">
                        <PrimaryButton
                            class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-bold transition-all transform active:scale-[0.98] shadow-lg shadow-emerald-200 uppercase tracking-widest"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Registrarme ahora
                        </PrimaryButton>
                    </div>
                </form>

                <div class="mt-12 text-center text-[10px] text-gray-400 uppercase tracking-widest font-semibold italic">
                    Al registrarte, aceptas nuestras condiciones de uso
                </div>
            </div>
        </div>
    </div>
</template>