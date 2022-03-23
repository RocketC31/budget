<script setup>
import { trans } from "matice";
import { Link, Head } from "@inertiajs/inertia-vue3";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

</script>
<template>
    <Head :title="trans('pages.reports')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('pages.reports') }}</h2>
            <div class="row row--gutter row--responsive mt-3">
                <div class="row__column">
                    <div class="card">
                        <Link :href="route('reports.show', { slug: 'weekly-report' })">{{ trans('reports.weekly_balance.title') }}</Link>
                        <p class="mt-1"><span v-html="trans('reports.weekly_balance.description')"></span></p>
                    </div>
                </div>
                <div class="row__column">
                    <div class="card">
                        <Link :href="route('reports.show', { slug: 'most-expensive-tags' })">{{ trans('reports.most_expensive_tags.title') }}</Link>
                        <p class="mt-1"><span v-html="trans('reports.most_expensive_tags.description') "></span></p>
                    </div>
                </div>
                <div class="row__column"></div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
