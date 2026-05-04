<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Mi Perfil" />

    <AuthenticatedLayout>
        <div class="py-12 mt-12 max-w-7xl mx-auto px-6 space-y-12">
            <!-- Sección: Información Personal -->
            <div class="bg-white dark:bg-slate-900 p-12 shadow-sm border border-slate-50 dark:border-slate-800 rounded-[4rem]">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-2xl"
                />
            </div>

            <!-- Sección: Seguridad -->
            <div class="bg-white dark:bg-slate-900 p-12 shadow-sm border border-slate-50 dark:border-slate-800 rounded-[4rem]">
                <UpdatePasswordForm class="max-w-2xl" />
            </div>

            <!-- Sección: Peligro -->
            <div class="bg-white dark:bg-slate-900 p-12 shadow-sm border border-slate-50 dark:border-slate-800 rounded-[4rem]">
                <DeleteUserForm class="max-w-2xl" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
