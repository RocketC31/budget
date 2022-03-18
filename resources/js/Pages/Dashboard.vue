<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { trans } from 'matice';
import { Inertia } from '@inertiajs/inertia';

defineProps({
    month: {
        type: Number
    },
    year: {
        type: Number,
        default: new Date().getFullYear()
    },
    widgets: {
        type: Array
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('general.dashboard') }}</h2>
            <p class="mt-1">{{ trans('calendar.months.' + month) }} {{ year }}</p>
            <div class="row row--gutter row--responsive my-3">
                <div v-for="widget in widgets" class="row__column">
                   {{ $inertia::visit(route('widgets.show', { id: widget.id })) }}
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
