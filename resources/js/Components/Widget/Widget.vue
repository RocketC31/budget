<script setup>
import Balance from "@/Components/Widget/Balance";

defineProps({
    type: {
        type: String,
        default: 'spent',
    },
    widget: {
        type: Object
    }
});
</script>

<template>
    <Balance></Balance>
</template>
