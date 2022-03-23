<script setup>
import { trans } from "matice";
import { Head } from "@inertiajs/inertia-vue3";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import TagPrice from "@/Components/Partials/TagPrice";

const props = defineProps({
    totalSpent: String,
    mostExpensiveTags: Array
});

</script>
<template>
    <Head :title="trans('pages.reports')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('activities.tag.most_expensive') }}</h2>
            <TagPrice :tags-price="mostExpensiveTags"></TagPrice>
        </div>
    </BreezeAuthenticatedLayout>
</template>
