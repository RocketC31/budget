<template>
    <Head :title="trans('models.' + transaction.type)" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ transaction.description }}</h2>
            <Attachments :element="transaction"></Attachments>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import Attachments from "@/Components/Partials/Attachments.vue";
    defineProps({
        transaction: Object
    });
</script>
