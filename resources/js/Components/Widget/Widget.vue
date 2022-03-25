<script setup>
import Balance from "@/Components/Widget/Balance";
import Spent from "@/Components/Widget/Spent";

defineProps({
    widget: {
        type: Object
    }
});
</script>

<template>
    <Balance v-if="widget.type === 'balance'" :currency-symbol="widget.currency_symbol" :balance="widget.balance"></Balance>
    <Spent v-if="widget.type === 'spent'" :period="widget.period" :spent="widget.spent" :currency-symbol="widget.currency_symbol"></Spent>
</template>
