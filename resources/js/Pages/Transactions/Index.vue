<script setup>
import { Link, Head } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import List from "@/Pages/Transactions/Partials/List";
import { ref } from "vue";
import Chart from "@/Pages/Transactions/Partials/Chart";
import ResumeTags from "@/Pages/Transactions/Partials/ResumeTags";
import EmptyState from "@/Components/Partials/EmptyState";

defineProps({
    tags: Array,
    currentMonthIndex: Number,
    month: String,
    year: String,
    transactions: Array,
    tagsPrice: Array,
    transactionsChart: Object,
    currency: String
});

const dataType = ref('list');

</script>

<template>
    <Head :title="trans('models.transactions')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3" id="transactions-page">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.transactions') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <Link :href="route('transactions.create')" class="button">{{ trans('actions.create') }} {{ trans('models.transactions') }}</Link>
                </div>
            </div>
            <div class="row md:flex-row flex-col">
                <div class="row__column md:mr-3 mb-2 md:max-w-xs w-full">
                    <div class="box">
                        <div class="box__section">
                            <div class="mb-2">
                                <Link :href="route('transactions.index')">{{ trans('actions.reset') }}</Link>
                            </div>
                            <span>{{ trans('activities.tag.filter') }}</span>
                            <div v-for="tag in tags" class="mt-1 ml-1">
                                <Link :href="route('transactions.index', { filterBy: { tag: tag.id}, monthIndex: currentMonthIndex })">{{ tag.name }}</Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row__column w-full">
                    <h2 class="mb-2">
                        <Link :href="route('transactions.index', { monthIndex: (currentMonthIndex - 1) })"><i class="fa fa-chevron-left"></i></Link>
                        {{ trans('calendar.months.' + month) }}, {{ year }}
                        <Link :href="route('transactions.index', { monthIndex: (currentMonthIndex + 1) })"><i class="fa fa-chevron-right"></i></Link>
                    </h2>
                    <template v-if="transactions.length > 0">
                        <ul class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 mb-2" id="transaction-tabs">
                            <li>
                                <a href="#" @click.stop="dataType = 'list'" aria-current="page" class="transaction-link inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg" :class="{ active: dataType === 'list' }">
                                    {{ trans('fields.list') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" @click.stop="dataType = 'chart'" class="transaction-link inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg" :class="{ active: dataType === 'chart' }">
                                    {{ trans('fields.chart') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" @click.stop="dataType = 'tags'" class="transaction-link inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg" :class="{ active: dataType === 'tags' }">
                                    {{ trans('models.tags') }}
                                </a>
                            </li>
                        </ul>
                        <List v-if="transactions && dataType === 'list'" :transactions="transactions" :currency="currency"></List>
                        <Chart v-if="transactionsChart && dataType === 'chart'" :transactions-chart="transactionsChart"></Chart>
                        <ResumeTags v-if="tagsPrice && dataType === 'tags'" :tags-price="tagsPrice" :currency="currency"></ResumeTags>
                    </template>
                    <EmptyState v-else :payload="'transactions'"></EmptyState>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
