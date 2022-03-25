<script setup>
import { Head } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import Layout from '@/Pages/Settings/Layout.vue';
import Wizard from "@/Components/Widget/Wizard";
import WizardList from "@/Components/Widget/List";

defineProps({
    types: Array,
    expectedProperties: Object,
    widgets: {
        type: Array,
        default: []
    }
});

</script>

<template>
    <Head :title="trans('general.dashboard')" />

    <Layout>
        <div class="mb-2">
            <Wizard :types="types" :expected-properties="expectedProperties"></Wizard>
        </div>
        <WizardList :widgets="widgets" :expected-properties="expectedProperties"></WizardList>
    </Layout>
</template>
