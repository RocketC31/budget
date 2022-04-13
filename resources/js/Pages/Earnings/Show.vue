<template>
    <Head :title="trans('models.earning')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ earning.description }}</h2>
            <Attachments :element="earning" :type="'earning'"></Attachments>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import Attachments from "@/Components/Partials/Attachments";

    defineProps({
        earning: Object
    });
</script>
