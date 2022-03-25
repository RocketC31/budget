<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import { Head } from '@inertiajs/inertia-vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

defineProps({
    budgets: Array,
    currency: String
});

</script>

<template>
    <Head :title="trans('models.budgets')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.budgets') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <Link :href="route('budgets.create')" class="button">{{ trans('actions.create') }} {{ trans('models.budget') }}</Link>
                </div>
            </div>
            <div class="box mt-3">
                <div v-if="budgets.length < 1" class="box__section text-center">{{ trans('general.empty_state', { resource: trans('models.budgets').toLowerCase() } ) }}</div>
                <div v-for="budget in budgets" class="box__section">
                    <div>{{ budget.tag.name }}</div>
                    <progress class="mt-2 mb-1" :value="budget.spent" :max="budget.amount"></progress>
                    <div style="font-size: 14px; font-weight: 600;"><span v-html="currency"></span> {{ budget.formatted_spent }} {{ trans('general.of') }} <span v-html="currency"></span> {{ budget.formatted_amount }}</div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
