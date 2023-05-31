<template>
    <Head :title="trans('models.budgets')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3 flex-wrap sm:flex-nowrap">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.budgets') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle w-full sm:w-auto flex items-center justify-between">
                    <Link :href="route('budgets.create')" class="button">{{ trans('actions.create') }} {{ trans('models.budget') }}</Link>
                </div>
            </div>
            <div class="box overflow-auto">
                <template v-if="budgets.length > 0">
                    <table class="w-full whitespace-nowrap">
                        <tbody>
                            <template v-for="budget in budgets" class="box__section row">
                                <tr tabindex="0" class="focus:outline-none h-16 border-y border-gray-100 dark:border-gray-700 rounded">
                                    <td class="px-3 w-5/12">
                                        {{ budget.tag.name }}
                                    </td>
                                    <td class="px-3 w-5/12">
                                        <progress class="mt-2 mb-1" :value="budget.spent" :max="budget.amount"></progress>
                                        <div style="font-size: 14px; font-weight: 600;"><span v-html="currency"></span> {{ budget.formatted_spent }} {{ trans('general.of') }} <span v-html="currency"></span> {{ budget.formatted_amount }}</div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>
                <EmptyState v-else :payload="'budgets'"></EmptyState>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { Link } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import { Head } from '@inertiajs/inertia-vue3';
    import EmptyState from "@/Components/Partials/EmptyState.vue";
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

    defineProps({
        budgets: Array,
        currency: String
    });
</script>
