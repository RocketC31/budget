<template>
    <div
        class="flex items-center gap-1 justify-center py-3 px-3 text-sm focus:outline-none leading-none rounded"
        :class="{'text-green-800 bg-green-300 dark:text-green-200 dark:bg-green-600' : type === 'earning', 'text-red-900 bg-red-300 dark:text-red-200 dark:bg-red-600' : type === 'spending'}"
    >
        <span v-html="currency"></span> {{ amount }}
    </div>
</template>

<script setup>
    const props = defineProps({
        type: String,
        amount: String,
        currency: String
    });
</script>
