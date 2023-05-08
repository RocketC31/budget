<template>
    <Balance v-if="widget.type === 'balance' || widget.type === 'balance_global'" :widget="widget"></Balance>
    <Spent v-if="widget.type === 'spent'" :widget="widget"></Spent>
</template>

<script setup>
    import Balance from "@/Components/Widget/Balance.vue";
    import Spent from "@/Components/Widget/Spent.vue";

    defineProps({
        widget: {
            type: Object
        }
    });
</script>
