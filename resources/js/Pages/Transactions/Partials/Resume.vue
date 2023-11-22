<template>
    <div class="flex items-center gap-2 rounded pt-4 pb-4 mt-2 mb-2">
        {{ trans('general.total_spent') }} :
        <Currency :currency="currency" :amount="totalSpent" class="dark:bg-dark-bg dark:text-white text-black"></Currency>
    </div>
    <div class="gap-2 grid overflow-auto">
        <div v-for="tag in tagsToShow" class="text-center box">
            <div class="head p-4 border-b dark:border-neutral-600">
                <Tag :tag="tag" class="justify-center"></Tag>
                <div class="flex items-center mt-2 text-sm justify-center">
                    {{ trans('general.total_spent') }} :
                    <Currency :currency="currency" :amount="getTotalFromTag(tag.id)" class="dark:bg-dark-bg dark:text-white text-black"></Currency>
                </div>
            </div>
            <div class="body p-2">
                <Link
                    v-for="transaction in getTransactionSpentFromTag(tag.id)"
                    :href="route('transactions.edit', { id: transaction.id, dataType: dataType })"
                    class="transaction mb-1 pb-2 pt-1 flex gap-2 items-center justify-between hover:text-gray-900 border-b last:border-b-0 dark:border-neutral-600 ease-in duration-200 dark:hover:text-gray-400 text-gray-700 dark:text-gray-500"
                >
                    <div
                        class="left w-2/3 flex items-center justify-between"
                    >
                        <div class="text-sm text-left">
                            <p class="font-medium leading-none" :title="transaction.description">
                                {{ truncate(transaction.description, 25) }}
                                <br> <span class="text-xs">{{ formatDate(transaction.happened_on, { dateStyle: 'short'}) }}</span>
                            </p>
                        </div>
                        <i v-if="transaction.recurring_id" class="fas fa-recycle"></i>
                    </div>
                    <div class="right text-sm w-1/3">
                        <Currency
                            :amount="transaction.formatted_amount"
                            :type="transaction.type"
                            :currency="currency"
                        ></Currency>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
<script setup>
    import Tag from "@/Components/Partials/Tag.vue";
    import Currency from "@/Pages/Transactions/Partials/Currency.vue";
    import { trans } from 'matice';
    import {formatDate, truncate, formattedAmount} from "@/tools";
    import {computed} from "vue";
    import {Link} from "@inertiajs/inertia-vue3";
    import {Inertia} from "@inertiajs/inertia";
    const props = defineProps({
        transactions: Array,
        dataType: String,
        tags: Array,
        totalSpent: String,
        currency: String
    });

    function getTransactionSpentFromTag(tagId) {
        return props.transactions.filter((t) => {
            return t.tag_id === tagId;
        })
    }

    function getTotalFromTag(tagId) {
        let total = 0;
        props.transactions.forEach((t) => {
            if (t.tag_id === tagId && t.type === "spending") {
                total += t.amount;
            }
        });
        return formattedAmount(total);
    }

    function hasTransactionsToShow(tagId) {
        let total = 0;
        props.transactions.forEach((t) => {
            if (t.tag_id === tagId) {
                total ++;
            }
        });
        return total;
    }

    const tagsToShow = computed(() => {
        let tags = [];
        props.tags.forEach((t) => {
            if (hasTransactionsToShow(t.id)) {
                tags.push(t);
            }
        });
        if (hasTransactionsToShow(null)) {
            tags.push({
                id: null,
                space_id: null,
                name: trans("general.other"),
                color: "C1C1C1FF"
            });
        }

        return tags;
    });
</script>
<style lang="scss" scoped>
.grid {
    grid-template-columns: repeat(auto-fill,minmax(250px,1fr));
}
</style>
