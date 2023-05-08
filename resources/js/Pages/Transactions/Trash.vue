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
                                            <Tag :tag="transaction.tag"></Tag>
                                        </td>
                                        <td class="px-3 w-10 text-center dark:text-gray-500">
                                            <i v-if="transaction.recurring_id" class="fas fa-recycle"></i>
                                        </td>
                                        <td class="px-3 text-center">
                                            <Currency :currency="currency" :amount="transaction.formatted_amount" :type="transaction.type"></Currency>
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
                        <EmptyState v-else :message="trans('general.empty_trash')" :create-link="false"></EmptyState>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { Head, Link } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import EmptyState from '@/Components/Partials/EmptyState.vue';
    import Tag from '@/Components/Partials/Tag.vue';
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { ref } from "vue";
    import { formatDate } from '@/tools';
    import { Inertia } from "@inertiajs/inertia";
    import Currency from "@/Pages/Transactions/Partials/Currency.vue";

    defineProps({
        transactions: Array,
        tagsPrice: Array,
        transactionsChart: Object,
        currency: String
    });

    const dataType = ref('list');

    function remove(transaction) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('transactions.purge', { id: transaction.id }));
        }
    }

    function removeAll() {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('transactions.purge_all'))
        }
    }

    function restore(transaction) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.post(route('transactions.restore', { id: transaction.id }));
        }
    }
</script>
