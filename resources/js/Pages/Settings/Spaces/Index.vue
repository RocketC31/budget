<template>
    <Head :title="trans('general.spaces_explanation')" />

    <Layout>
        <Link class="button mb-2" :href="route('spaces.create')">{{ trans('actions.create') }}</Link>
        <div class="box">
            <ul class="box__section">
                <li v-for="space in spaces" class="row row--middle">
                    <div class="row__column">{{ space.name }} &middot; {{ capitalize(space.pivot.role) }}</div>
                    <div class="row__column row__column--compact">
                        <Link v-if="space.pivot.role === 'admin'" class="button button--secondary button--small" :href="route('spaces.edit', { space: space.id })">{{ trans('pages.settings') }}</Link>
                    </div>
                </li>
            </ul>
        </div>
    </Layout>
</template>

<script setup>
    import { Head, Link } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import Layout from '@/Pages/Settings/Layout.vue';
    import { capitalize } from '@/tools';

    defineProps({
        spaces: {
            type: Array,
            default: []
        }
    });
</script>
