<template>
    <Head :title="trans('models.transactions')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3" id="transactions-page">
            <div class="row mb-3 flex-wrap sm:flex-nowrap">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.transactions') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle w-full sm:w-auto flex items-center justify-between">
                    <Link :href="route('transactions.trash')" class="m-0 sm:m-3">{{ trans('activities.trashes.index') }}</Link>
                    <Link :href="route('transactions.create', { dataType: dataType })" class="button">{{ trans('actions.create') }} {{ trans('models.transaction') }}</Link>
                </div>
            </div>
            <div class="row flex-col">
                <div class="row__column md:mr-3 mb-2 w-full">
                    <div class="box">
                        <div class="box__section">
                            <div class="mb-2">
                                <Link :href="route('transactions.index')">{{ trans('actions.reset') }}</Link>
                            </div>
                            <span>{{ trans('activities.tag.filter') }}</span>
                            <div class="flex gap-2 flex-wrap">
                                <div v-for="tag in tags" class="m-1 rounded">
                                    <Button @click="toggleTag(tag.id)" :active="tagIsInFilterBy(tag.id)">
                                        <Tag :tag="tag"></Tag>
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <div class="box__section">
                            <input type="text" :placeholder="trans('actions.search')" v-model="filterBy.search" id="search" @keyup.stop="search()">
                        </div>
                    </div>
                </div>
                <div class="row__column w-full mt-2">
                    <h2 class="mb-2">
                        <Link :href="route('transactions.index', { monthIndex: (currentMonthIndex - 1) })"><i class="fa fa-chevron-left"></i></Link>
                        {{ trans('calendar.months.' + month) }} {{ year }}
                        <Link :href="route('transactions.index', { monthIndex: (currentMonthIndex + 1) })"><i class="fa fa-chevron-right"></i></Link>
                    </h2>
                    <template v-if="transactions.length > 0">
                        <ul class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 mb-2" id="transaction-tabs">
                            <li>
                                <a href="#" @click.stop="dataType = 'resume'" aria-current="page" class="transaction-link inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg" :class="{ active: dataType === 'resume' }">
                                    {{ trans('fields.resume') }}
                                </a>
                            </li>
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
                        <Resume
                            v-if="transactions && dataType === 'resume'"
                            :transactions="transactions"
                            :currency="currency" :tags="tags"
                            :total-spent="totalSpent"
                            :data-type="dataType"
                        ></Resume>
                        <List
                            v-if="transactions && dataType === 'list'"
                            :transactions="transactions"
                            :currency="currency"
                            :tags="tags"
                            :data-type="dataType"
                        ></List>
                        <Chart v-if="transactionsChart && dataType === 'chart'" :transactions-chart="transactionsChart"></Chart>
                        <ResumeTags v-if="tagsPrice && dataType === 'tags'" :tags-price="tagsPrice" :currency="currency"></ResumeTags>
                    </template>
                    <EmptyState v-else :payload="'transactions'"></EmptyState>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import {Link, Head} from "@inertiajs/inertia-vue3";
    import {trans} from 'matice';
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import List from "@/Pages/Transactions/Partials/List.vue";
    import {onMounted} from "vue";
    import Chart from "@/Pages/Transactions/Partials/Chart.vue";
    import ResumeTags from "@/Pages/Transactions/Partials/ResumeTags.vue";
    import EmptyState from "@/Components/Partials/EmptyState.vue";
    import Resume from "@/Pages/Transactions/Partials/Resume.vue";
    import Tag from "@/Components/Partials/Tag.vue";
    import {debounce} from "@/tools";
    import {Inertia} from "@inertiajs/inertia";
    import Button from "@/Components/Button.vue";

    const props = defineProps({
        tags: Array,
        currentMonthIndex: Number,
        totalSpent: String,
        month: String,
        year: String,
        transactions: Array,
        tagsPrice: Array,
        transactionsChart: Object,
        filterBy: Object,
        currency: String,
        dataType: {
            type: String,
            default: 'resume'
        }
    });

    const debounceFunction = debounce(() => {
        runSearch();
    }, 500);

    function search() {
        if (props.filterBy.search) {
            debounceFunction()
        }
    }

    function runSearch() {
        const filterBy = {};
        if (props.filterBy.tag) {
            filterBy.tag = props.filterBy.tag;
        }

        if (props.filterBy.search) {
            filterBy.search = props.filterBy.search;
        }

        Inertia.visit(
            route('transactions.index'),
            {
                data: {
                    filterBy: filterBy,
                    monthIndex: props.currentMonthIndex,
                    dataType: props.dataType
                }
            }
        )
    }

    function tagIsInFilterBy(tagId) {
        return getCurrentTags().indexOf(tagId.toString()) > -1;
    }

    function getCurrentTags() {
        let currentTags = props.filterBy.tag;
        if (!currentTags) {
            currentTags = [];
        } else {
            currentTags = currentTags.split(',');
        }
        return currentTags;
    }

    function toggleTag(tagId) {
        const currentTags = getCurrentTags();
        const index = currentTags.indexOf(tagId.toString());
        if (index > -1) {
            currentTags.splice(index, 1);
        } else {
            currentTags.push(tagId);
        }

        props.filterBy.tag = currentTags.toString();
        runSearch();
    }

    onMounted(() => {
        if (props.filterBy.search === undefined) {
            props.filterBy.search = "";
        }
    })
</script>
<style lang="scss" scoped>
.box {
    .box__section {
        padding: 10px 20px;
    }
}
</style>
