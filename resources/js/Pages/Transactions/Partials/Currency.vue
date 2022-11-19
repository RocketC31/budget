<template>
    <div class="py-3 px-3 text-sm focus:outline-none leading-none rounded" :class="{'text-green-700 bg-green-300 dark:text-green-200 dark:bg-green-600' : type === 'earning', 'text-red-800 bg-red-400 dark:text-red-200 dark:bg-red-600' : type === 'spending'}">
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
