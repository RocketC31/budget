<script setup>
import { trans } from 'matice';

defineProps({
    widget:Object

});
</script>

<template>
    <div class="card card--red">
        <h2 style="font-size: 20px;"><span v-html="widget.currency_symbol"></span> {{ widget.spent }}</h2>
        <div class="mt-1">{{ trans('configuration.widget.' + widget.type) }} {{ trans('calendar.' + widget.period).toLowerCase() }}</div>
    </div>
</template>
