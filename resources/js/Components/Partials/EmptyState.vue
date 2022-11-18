<template>
    <div class="box__section text-center">
        <div v-if="payload" class="mb-1">
            {{ trans('general.empty_state', { args: { resource: trans('models.' + payload).toLowerCase() } }) }}
        </div>
        <div v-else-if="message" class="mb-1">
            {{ message }}
        </div>
        <Link v-if="createLink" :href="route(payload + '.create')" style="font-size: 14px;">{{ trans('actions.create') }}</Link>
    </div>
</template>

<script setup>
    import { trans } from "matice";
    import { Link } from "@inertiajs/inertia-vue3";

    defineProps({
        payload: String,
        message: String,
        createLink: { type: Boolean, default: true },
    });
</script>
