<template>
    <Head :title="trans('models.spending')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ spending.description }}</h2>
            <Attachments :element="spending" :type="'spending'"></Attachments>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import Attachments from "@/Components/Partials/Attachments";

    defineProps({
        spending: Object
    });
</script>
