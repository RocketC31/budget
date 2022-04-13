<template>
    <Head :title="trans('pages.trash') + ' ' + trans('models.transactions')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3" id="transactions-page">
            <div class="row mb-3 flex-wrap sm:flex-nowrap">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('pages.trash') }} {{ trans('models.transactions') }}</h2>
                    <Link :href="route('transactions.index')"><i class="fa fa-chevron-left"></i> {{ trans('actions.back') }}</Link>
                </div>
                <div class="row__column row__column--compact row__column--middle w-full sm:w-auto flex items-center justify-between">
                    <div @click.stop="removeAll()" v-if="transactions.length > 0" class="m-0 sm:m-3 cursor-pointer">{{ trans('actions.remove_all') }}</div>
                </div>
            </div>
            <div class="row md:flex-row flex-col">
                <div class="row__column w-full">
                    <div class="box transaction-block overflow-auto" id="transaction-list">
                        <template v-if="transactions.length > 0">
                            <table class="w-full whitespace-nowrap">
                                <tbody>
                                <template v-for="transaction in transactions">
                                    <tr tabindex="0" class="focus:outline-none h-16 border-y border-gray-100 dark:border-gray-700 rounded">
                                        <td class="px-1">
                                            <div class="flex items-center pl-5">
                                                <p class="text-base font-medium leading-none text-gray-700 dark:text-gray-500 mr-2">
                                                    {{ transaction.description }}
                                                    <br> <span class="text-sm text-gray-400">{{ trans('fields.deletion_date') }} : {{ formatDate(transaction.deleted_at) }}</span>
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="flex items-center dark:text-gray-500">
                                                <template v-if="transaction.tag">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M9.16667 2.5L16.6667 10C17.0911 10.4745 17.0911 11.1922 16.6667 11.6667L11.6667 16.6667C11.1922 17.0911 10.4745 17.0911 10 16.6667L2.5 9.16667V5.83333C2.5 3.99238 3.99238 2.5 5.83333 2.5H9.16667" :stroke="'#'+ transaction.tag.color" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <circle cx="7.50004" cy="7.49967" r="1.66667" :stroke="'#'+ transaction.tag.color" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></circle>
                                                    </svg>
                                                    <p class="ml-1">{{ transaction.tag.name }}</p>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-3 w-10 text-center dark:text-gray-500">
                                            <i v-if="transaction.recurring_id" class="fas fa-recycle"></i>
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="py-3 px-3 text-sm focus:outline-none leading-none rounded" :class="{'text-green-700 bg-green-300 dark:text-green-200 dark:bg-green-600' : transaction.type === 'earnings', 'text-red-800 bg-red-400 dark:text-red-200 dark:bg-red-600' : transaction.type === 'spendings'}">
                                                <span v-html="currency"></span> {{ transaction.formatted_amount }}
                                            </div>
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="flex items-center justify-around focus:ring-2 focus:ring-offset-2 focus:ring-red-300 text-sm leading-none text-gray-600 dark:text-gray-200 dark:bg-gray-600 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none">
                                                <div class="p-3 cursor-pointer" @click.stop="restore(transaction)">
                                                    <i class="fas fa-trash-undo fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                </div>
                                                <div class="p-3 cursor-pointer" @click.stop="remove(transaction)">
                                                    <i class="fas fa-trash fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </template>
                        <div v-else class="box__section text-center">
                            <div class="mb-1">{{ trans('general.empty_trash') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { Head, Link } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { ref } from "vue";
    import { formatDate } from '@/tools';
    import { Inertia } from "@inertiajs/inertia";

    defineProps({
        transactions: Array,
        tagsPrice: Array,
        transactionsChart: Object,
        currency: String
    });

    const dataType = ref('list');

    function remove(transaction) {
        let url = `/${transaction.type}/${transaction.id}/purge`;
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(url);
        }
    }

    function removeAll() {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('transactions.purge_all'))
        }
    }

    function restore(transaction) {
        let url = `/${transaction.type}/${transaction.id}/restore`;
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.post(url);
        }
    }
</script>
