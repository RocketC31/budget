<template>
    <Head :title="trans('general.dashboard')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('general.dashboard') }}</h2>
            <p class="mt-1">{{ trans('calendar.months.' + month) }} {{ year }}</p>
            <div class="row row--gutter row--responsive my-3">
                <div v-for="widget in widgets" class="row__column">
                    <Widget :widget="widget"></Widget>
                </div>
            </div>
            <div v-if="mostExpensiveTags.length > 0" class="box mt-3">
                <div class="box__section box__section--header">{{ trans('activities.tag.most_expensive') }}</div>
                <div v-for="(tag) in mostExpensiveTags" class="box__section row row--seperate">
                    <div class="row__column row__column--middle">
                        <Tag :tag="tag"></Tag>
                    </div>
                    <div class="row__column row__column--middle">
                        <progress :max="totalSpent" :value="tag.amount"></progress>
                    </div>
                    <div class="row__column row__column--middle text-right"><span v-html="currency"></span> {{ formattedAmount(tag.amount) }}</div>
                </div>
            </div>
            <div class="box mt-3">
                <div class="box__section box__section--header">{{ trans('activities.balance.daily') }}</div>
                <Chart :additional-class="'box__section'" :data="dailyBalance" :labels="rangeOfDays(daysInMonth)" :label="trans('activities.balance.daily')"></Chart>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { trans } from 'matice';
    import { formattedAmount, rangeOfDays } from '@/tools';
    import { Head } from '@inertiajs/inertia-vue3';
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Tag from '@/Components/Partials/Tag.vue';
    import Widget from "@/Components/Widget/Widget";
    import Chart from "@/Components/Partials/Chart";

    defineProps({
        month: Number,
        year: {
            type: Number,
            default: new Date().getFullYear()
        },
        widgets: Array,
        mostExpensiveTags: Array,
        daysInMonth: Number,
        dailyBalance: Object,
        totalSpent: {
            default: 0
        },
        currency: String
    });
</script>
