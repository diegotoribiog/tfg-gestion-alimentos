<script setup>
import { ref, onMounted } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    productosUrgentes: { type: Array, default: () => [] },
});

const showModal = ref(false);
const noMostrarMas = ref(false);

onMounted(() => {
    const noAvisar = localStorage.getItem('no_avisar_caducidad');
    if (props.productosUrgentes.length > 0 && !noAvisar) {
        showModal.value = true;
    }
});

const cerrarModal = () => {
    if (noMostrarMas.value) {
        localStorage.setItem('no_avisar_caducidad', 'true');
    }
    showModal.value = false;
};
</script>

<template>
    <Modal :show="showModal" @close="cerrarModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                ¡Atención! Tienes productos caducados o por caducar
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Revisa tu inventario para evitar desperdicio alimentario.
            </p>
            
            <div class="mt-4 space-y-2">
                <div v-for="producto in productosUrgentes.slice(0, 3)" :key="producto.id" class="text-sm">
                    {{ producto.nombre }} - Caduca: {{ producto.fecha_caducidad }}
                </div>
            </div>

            <div class="mt-6 flex items-center">
                <Checkbox v-model:checked="noMostrarMas" id="no_mostrar" />
                <label for="no_mostrar" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                    No volver a mostrar este aviso
                </label>
            </div>

            <div class="mt-6 flex justify-end">
                <PrimaryButton @click="cerrarModal">Entendido</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
