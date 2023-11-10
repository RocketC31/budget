<template>
    <Head :title="trans('models.recurrings')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.recurrings') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <Link :href="route('recurrings.trash')" class="m-0 sm:m-3">{{ trans('activities.trashes.index') }}</Link>
                </div>
            </div>
            <div class="box mt-3">
                <div class="row__column w-full">
                    <template v-if="recurrings.length > 0">
                        <div class="transaction-block overflow-auto" id="transaction-list">
                            <table class="w-full whitespace-nowrap">
                                <tbody>
                                    <template v-for="recurring in recurrings" :key="`${recurring.index}`">
                                        <tr tabindex="0" class="focus:outline-none h-12 border-y border-gray-100 dark:border-gray-700 rounded">
                                            <td class="px-1 w-7/12">
                                                <Link :href="route('recurrings.edit', { id: recurring.id })">
                                                    <div class="flex items-center pl-5">
                                                        <p class="text-base font-medium leading-none text-gray-700 hover:text-gray-900 dark:text-gray-500 mr-2 ease-in duration-200 dark:hover:text-gray-400" :title="recurring.description">
                                                            {{ truncate(recurring.description, 60) }}
                                                        </p>
                                                    </div>
                                                </Link>
                                            </td>
                                            <td class="text-sm">
                                                <template v-if="recurring.last_used_on">
                                                    {{ formatDate(recurring.last_used_on, { dateStyle: 'short'}) }}
                                                </template>
                                            </td>
                                            <td class="px-3 text-center">
                                                <Tag :tag="recurring.tag"></Tag>
                                            </td>
                                            <td class="px-3 text-center">
                                                <div class="flex items-center dark:text-gray-500">
                                                    <div class="py-3 px-3 text-sm focus:outline-none leading-none rounded text-blue-700 bg-blue-300 dark:text-blue-200 dark:bg-blue-600">
                                                        {{ trans('calendar.intervals.' + recurring.interval) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 text-center">
                                                <Currency  :currency="currency"  :amount="recurring.formatted_amount" :type="recurring.type"></Currency>
                                            </td>
                                            <td class="px-3 text-center">
                                                <div class="p-3 cursor-pointer" @click.stop="remove(recurring)">
                                                    <i class="fas fa-trash fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <EmptyState v-else :payload="'recurrings'" :create-link="false"></EmptyState>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Link, Head } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import {formatDate, truncate} from '@/tools';
    import EmptyState from "@/Components/Partials/EmptyState.vue";
    import Tag from '@/Components/Partials/Tag.vue';
    import {Inertia} from "@inertiajs/inertia";
    import Currency from "@/Pages/Transactions/Partials/Currency.vue";

    defineProps({
        recurrings: Array,
        currency: String
    });

    function remove(recurring) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('recurrings.delete', { recurring: recurring }));
        }
    }
</script>
