<template>
    <div class="card card--blue">
        <h2 class="flex items-center">
            <div v-html="widget.currency_symbol"></div>
            <div class="balance">{{ widget.balance }}</div>
            <template v-if="widget.can_refresh">
                <button @click="refreshBalance" class="transition ease-in-out delay-50 hover:text-green-400"><i class="far fa-sync"></i></button>
            </template>
        </h2>
        <div class="mt-1">{{ trans('configuration.widget.' + widget.type) }}</div>
    </div>
</template>

<script setup>
    import { trans } from "matice";
    import { Inertia } from "@inertiajs/inertia";

    const props = defineProps({
        widget: Object
    });

    function refreshBalance() {
        Inertia.get(route("widgets.refresh", { widget: props.widget.id }));
    }
</script>

<style lang="scss" scoped>
    h2 {
        font-size: 20px;
        .balance {
            margin: 0 5px;
        }
        button {
            font-size: 14px;
        }
    }
</style>
