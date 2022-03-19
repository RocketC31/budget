<script setup>
import { trans } from 'matice';

defineProps({
    spent: String,
    currencySymbol: String,
    period: String

});
</script>

<template>
    <div class="card card--red">
        <h2 style="font-size: 20px;"><span v-html="currencySymbol"></span> {{ spent }}</h2>
        <div class="mt-1">{{ trans('general.spent') }} {{ trans('calendar.' + period).toLowerCase() }}</div>
    </div>
</template>
