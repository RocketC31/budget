<template>
    <tr tabindex="0" class="focus:outline-none h-16 border-y border-gray-100 dark:border-gray-700 rounded">
        <td class="px-1">
            <div class="flex items-center pl-5">
                <p class="text-base font-medium leading-none text-gray-700 dark:text-gray-500 mr-2">
                    {{ transaction.description }}
                    <br> <span class="text-sm text-gray-400">{{ formatDate(transaction.happened_on) }}</span>
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
                <Link class="p-3" :href="route(transaction.type + '.show', { id: transaction.id })">
                    <i class="fas fa-info-circle fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                </Link>
                <Link class="p-3" :href="route(transaction.type + '.edit', { id: transaction.id })">
                    <i class="fas fa-pencil fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                </Link>
                <div class="p-3 cursor-pointer" @click.stop="remove()">
                    <i class="fas fa-trash fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                </div>
            </div>
        </td>
    </tr>
</template>

<script setup>
    import { formatDate } from '@/tools';
    import { trans } from "matice";
    import { Link } from "@inertiajs/inertia-vue3";
    import { Inertia } from '@inertiajs/inertia'

    const props = defineProps({
        transaction: Object,
        currency: String
    });

    function remove() {
        let url = `/${props.transaction.type}/${props.transaction.id}`;
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(url);
        }
    }
</script>
