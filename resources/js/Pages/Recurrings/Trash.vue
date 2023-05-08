<template>
    <Head :title="trans('pages.trash') + ' ' + trans('models.recurrings')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('pages.trash') }} {{ trans('models.recurrings') }}</h2>
                    <Link :href="route('recurrings.index')"><i class="fa fa-chevron-left"></i> {{ trans('actions.back') }}</Link>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <div @click.stop="removeAll()" v-if="recurrings.length > 0" class="m-0 sm:m-3 cursor-pointer">{{ trans('actions.remove_all') }}</div>
                </div>
            </div>
            <div class="box mt-3">
                <div class="row__column w-full">
                    <template v-if="recurrings.length > 0">
                        <div class="transaction-block overflow-auto" id="transaction-list">
                            <table class="w-full whitespace-nowrap">
                                <tbody>
                                <template v-for="recurring in recurrings">
                                    <tr tabindex="0" class="focus:outline-none h-16 border-y border-gray-100 dark:border-gray-700 rounded">
                                        <td class="px-1">
                                            <div class="flex items-center pl-5">
                                                <p class="text-base font-medium leading-none text-gray-700 dark:text-gray-500 mr-2">
                                                    {{ recurring.description }}
                                                    <br> <span class="text-sm text-gray-400">{{ trans('fields.deletion_date') }} : {{ formatDate(recurring.deleted_at) }}</span>
                                                </p>
                                            </div>
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
                                            <div class="py-3 px-3 text-sm focus:outline-none leading-none rounded" :class="{'text-green-700 bg-green-300 dark:text-green-200 dark:bg-green-600' : recurring.type === 'earning', 'text-red-800 bg-red-400 dark:text-red-200 dark:bg-red-600' : recurring.type === 'spending'}">
                                                <span v-html="currency"></span> {{ recurring.formatted_amount }}
                                            </div>
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="flex items-center justify-around focus:ring-2 focus:ring-offset-2 focus:ring-red-300 text-sm leading-none text-gray-600 dark:text-gray-200 dark:bg-gray-600 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none">
                                                <div class="p-3 cursor-pointer" @click.stop="restore(recurring)">
                                                    <i class="fas fa-trash-undo fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                </div>
                                                <div class="p-3 cursor-pointer" @click.stop="remove(recurring)">
                                                    <i class="fas fa-trash fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <EmptyState v-else :message="trans('general.empty_trash')" :create-link="false"></EmptyState>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Link, Head } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import { formattedAmount, formatDate } from '@/tools';
    import Tag from "@/Components/Partials/Tag.vue";
    import EmptyState from "@/Components/Partials/EmptyState.vue";
    import {Inertia} from "@inertiajs/inertia";

    defineProps({
        recurrings: Array,
        currency: String
    });

    function remove(recurring) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('recurrings.purge', { recurring : recurring }));
        }
    }

    function removeAll() {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('recurrings.purge_all'))
        }
    }

    function restore(recurring) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.post(route('recurrings.restore', { recurring: recurring }));
        }
    }
</script>
