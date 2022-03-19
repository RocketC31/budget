<script setup>
import { trans } from "matice";

defineProps({
    balance: String,
    currencySymbol: String
})
</script>
<template>
    <div class="card card--blue">
        <h2 style="font-size: 20px;"><span v-html="currencySymbol"></span> {{ balance }}</h2>
        <div class="mt-1">{{ trans('general.balance') }}</div>
    </div>
</template>
