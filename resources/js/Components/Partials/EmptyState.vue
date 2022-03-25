<script setup>
import { trans } from "matice";
import { Link } from "@inertiajs/inertia-vue3";

defineProps({
    payload: String
})
</script>
<template>
    <div class="box__section text-center">
        <div class="mb-1">{{ trans('general.empty_state', { args: { resource: trans('models.' + payload).toLowerCase() } }) }}</div>
        <Link :href="route(payload + '.create')" style="font-size: 14px;">{{ trans('actions.create') }}</Link>
    </div>
</template>
