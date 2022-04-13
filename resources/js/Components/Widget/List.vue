<template>
    <div class="box">
        <div v-for="widget in widgets" :key="widget.id" class="box__section row">
            <div class="row__column row__column--compact mr-1">
                #{{ (widget.sorting_index + 1) }}
            </div>
            <div class="row__column">
                {{ trans('configuration.widget.' + widget.type) }}
                <template v-if="widget.type === 'spent'">
                    ({{ trans('calendar.'+widget.period) }})
                </template>
            </div>
            <div class="row__column">
                <button class="button link mr-1" @click="up(widget.id)">
                    <i class="fas fa-arrow-alt-up"></i>
                </button>
                <button class="button link" @click="down(widget.id)">
                    <i class="fas fa-arrow-alt-down"></i>
                </button>
            </div>
            <div class="row__column row__column--compact">
                <button class="button link" @click="remove(widget.id)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div v-if="widgets.length === 0" class="box__section text-center">{{ trans('activities.widgets.no_widgets') }}</div>
    </div>
</template>

<script setup>
    import { trans } from "matice";
    import {Inertia} from "@inertiajs/inertia";

    const props = defineProps({
        widgets: {
            type: Array,
            default: []
        },
        expectedProperties: {
            type: Object,
            default: {}
        }
    })

    function up(id) {
        Inertia.post(route('widgets.up', { widget: id}));
    }

    function down(id) {
        Inertia.post(route('widgets.down', { widget: id}));
    }

    function remove(id) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('widgets.delete', { widget : id }));
        }
    }
</script>
