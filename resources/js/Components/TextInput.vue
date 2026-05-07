<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-midnight/50 text-slate-900 dark:text-slate-300 placeholder-slate-400 dark:placeholder-slate-700 shadow-sm focus:ring-2 focus:ring-emerald-vibrant/20 transition-all"
        v-model="model"
        ref="input"
    />
</template>
