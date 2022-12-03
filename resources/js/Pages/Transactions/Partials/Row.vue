<template>
    <tr tabindex="0" class="focus:outline-none h-16 border-y border-gray-100 dark:border-gray-700 rounded">
        <td class="px-1">
            <div class="flex items-center pl-5">
                <p class="text-base font-medium leading-none text-gray-700 dark:text-gray-500 mr-2" :title="transaction.description">
                    {{ truncate(transaction.description, 25) }}
                    <br> <span class="text-sm text-gray-400">{{ formatDate(transaction.happened_on) }}</span>
                </p>
            </div>
        </td>
        <td class="px-3 text-center">
            <BreezeDropdown align="bottom" width="48" :use-as-relative="false" v-if="tags.length > 0">
                <template #trigger>
                    <span class="inline-flex rounded-md w-full">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 focus:outline-none transition ease-in-out duration-150">
                            <Tag v-if="transaction.tag" :tag="transaction.tag"></Tag>
                            <div v-else>
                                {{ trans("actions.choose_tag") }}
                            </div>
                        </button>
                    </span>
                </template>

                <template #content class="ho">
                    <div v-for="tag in tagsChoice" @click.prevent="changeTag(tag)" class="cursor-pointer block w-full px-4 py-2 text-left text-sm leading-5 text-gray-400 dark:text-white hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-white rounded focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                        <Tag :tag="tag"></Tag>
                    </div>
                </template>
            </BreezeDropdown>
        </td>
        <td class="px-3 w-10 text-center dark:text-gray-500">
            <i v-if="transaction.recurring_id" class="fas fa-recycle"></i>
        </td>
        <td class="px-3 text-center">
           <Currency :currency="currency" :amount="transaction.formatted_amount" :type="transaction.type"></Currency>
        </td>
        <td class="px-3 text-center">
            <div class="flex items-center justify-around focus:ring-2 focus:ring-offset-2 focus:ring-red-300 text-sm leading-none text-gray-600 dark:text-gray-200 dark:bg-gray-600 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none">
                <Link class="p-3" :href="route('transactions.show', { id: transaction.id })">
                    <i class="fas fa-info-circle fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                </Link>
                <Link class="p-3" :href="route('transactions.edit', { id: transaction.id })">
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
    import { formatDate, truncate } from '@/tools';
    import { trans } from "matice";
    import {Link, usePage} from "@inertiajs/inertia-vue3";
    import Tag from '@/Components/Partials/Tag';
    import BreezeDropdown from '@/Components/Dropdown.vue';
    import Currency from './Currency';
    import { Inertia } from '@inertiajs/inertia'
    import { computed } from "vue";

    const props = defineProps({
        transaction: Object,
        tags: Array,
        currency: String
    });

    const patchMethodAvailable = computed(() => usePage().props.value.patchMethodAvailable);

    const tagsChoice = computed(() => {
        return [{name: "--", id: null }].concat(props.tags);
    });

    function remove() {
        let url = `/transactions/${props.transaction.id}`;
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(url);
        }
    }

    function changeTag(tag) {
        if (tag.id === null) {
            props.transaction.tag = null;
            props.transaction.tag_id = null;
        } else {
            props.transaction.tag = tag;
            props.transaction.tag_id = tag.id;
        }
        if (patchMethodAvailable.value) {
            Inertia.patch(route('transactions.update_tag', { transaction: props.transaction.id }), props.transaction, { preserveState: true });
        } else {
            Inertia.put(route('transactions.update_tag', { transaction: props.transaction.id }), props.transaction, { preserveState: true });
        }
    }
</script>
