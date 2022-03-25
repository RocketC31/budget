<script setup>
import { trans } from "matice";
import { formatDate } from '@/tools';
import { Head, Link } from "@inertiajs/inertia-vue3";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

const props = defineProps({
    activities: Array
});

</script>
<template>
    <Head :title="trans('pages.activities')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('pages.activities') }}</h2>
            <div class="box mt-3">
                <div v-for="activity in activities" class="box__section row">
                    <div class="row__column row__column--compact mr-2" style="width: 25px;">
                        <img v-if="activity.user" class="avatar" :src="activity.user.avatar" />
                    </div>
                    <div class="row__column row__column--middle">
                        {{ trans('activities.' + activity.action) }}
                        <Link :href="'/'+activity.entity_type+'s/'+ activity.entity_id">#{{ activity.entity_id }}</Link>
                    </div>
                    <div class="row__column row__column--middle row__column--compact">{{ formatDate(activity.created_at) }}</div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
