<template>
    <Head :title="trans('pages.reports')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('fields.weekly_report') }} {{ year }}</h2>
            <div class="row mt-1">
                <Link class="mr-1" :href="route('reports.show', { slug: 'weekly-report', year: (parseInt(year) - 1) })">{{ trans('actions.previous') }}</Link>
                <Link :href="route('reports.show', { slug: 'weekly-report', year: (parseInt(year) + 1) })">{{ trans('actions.next') }}</Link>
            </div>
            <div class="box mt-3">
                <div class="box__section">
                    <div class="ct-chart ct-major-twelfth"></div>
                    <Chart :additional-class="'box__section'" :data="data.series" :labels="data.labels" :label="trans('fields.weekly_report')"></Chart>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { trans } from "matice";
    import { Link, Head } from "@inertiajs/inertia-vue3";
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Chart from "@/Components/Partials/Chart";

    const props = defineProps({
        year: String,
        weeks: Object
    });

    const data = {
        labels: Object.keys(props.weeks),
        series: props.weeks
    };
</script>
