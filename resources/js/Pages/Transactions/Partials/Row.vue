<template>
    <tr tabindex="0" class="focus:outline-none h-12 border-y border-gray-100 dark:border-gray-700 rounded">
        <td class="px-1 w-7/12">
            <Link
                :href="route('transactions.edit', { id: transaction.id })"
                :data="{ dataType: dataType }"
            >
                <div class="flex items-center pl-5">
                    <p class="text-base font-medium leading-none text-gray-700 hover:text-gray-900 dark:text-gray-500 mr-2 ease-in duration-200 dark:hover:text-gray-400" :title="transaction.description">
                        {{ truncate(transaction.description, 60) }}
                    </p>
                </div>
            </Link>
        </td>
        <td class="text-sm">
            {{ formatDate(transaction.happened_on, { dateStyle: 'short'}) }}
        </td>
        <td class="px-3 text-center">
            <Tag v-if="transaction.tag" :tag="transaction.tag"></Tag>
        </td>
        <td class="px-3 w-10 text-center dark:text-gray-500">
            <i v-if="transaction.recurring_id" class="fas fa-recycle"></i>
        </td>
        <td class="px-3 text-center">
           <Currency :currency="currency" :amount="transaction.formatted_amount" :type="transaction.type"></Currency>
        </td>
        <td class="px-3 text-center">
            <div class="p-3 cursor-pointer" @click.stop="removeTransaction(transaction.id, dataType)">
                <i class="fas fa-trash fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
            </div>
        </td>
    </tr>
</template>

<script setup>
import {formatDate, removeTransaction, truncate} from '@/tools';
import {Link,} from "@inertiajs/inertia-vue3";
import Tag from '@/Components/Partials/Tag.vue';
import Currency from './Currency.vue';

const props = defineProps({
    transaction: Object,
    tags: Array,
    dataType: String,
    currency: String
});
</script>
