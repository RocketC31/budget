<template>
    <Head :title="trans('pages.trash') + ' ' + trans('models.tags')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('pages.trash') }} {{ trans('models.tags') }}</h2>
                    <Link :href="route('tags.index')"><i class="fa fa-chevron-left"></i> {{ trans('actions.back') }}</Link>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <div @click.stop="removeAll()" v-if="tags.length > 0" class="m-0 sm:m-3 cursor-pointer">{{ trans('actions.remove_all') }}</div>
                </div>
            </div>
            <div class="box overflow-auto">
                <template v-if="tags.length > 0">
                    <table class="w-full whitespace-nowrap">
                        <tbody>
                        <template v-for="tag in tags" class="box__section row">
                            <tr tabindex="0" class="focus:outline-none h-16 border-y border-gray-100 dark:border-gray-700 rounded">
                                <td class="px-3 w-9/12">
                                    <Tag :tag="tag"></Tag>
                                </td>
                                <td class="px-3 text-center">
                                    <div class="flex items-center justify-around focus:ring-2 focus:ring-offset-2 focus:ring-red-300 text-sm leading-none text-gray-600 dark:text-gray-200 dark:bg-gray-600 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none">
                                        <div class="p-3 cursor-pointer" @click.stop="restore(tag)">
                                            <i class="fas fa-trash-undo fa-xs c-light ml-1 dark:hover:text-gray-100"></i>
                                        </div>
                                        <div class="p-3 cursor-pointer" @click.stop="remove(tag)">
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
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { Link, Head } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import EmptyState from "@/Components/Partials/EmptyState.vue";
    import Tag from '@/Components/Partials/Tag.vue';
    import { Inertia } from "@inertiajs/inertia";

    defineProps({
        tags: Array,
    });

    function remove(tag) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('tags.purge', { tag: tag }));
        }
    }

    function removeAll() {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.delete(route('tags.purge_all'));
        }
    }

    function restore(tag) {
        if (confirm(trans('actions.confirm_action'))) {
            Inertia.post(route('tags.restore', { tag: tag }));
        }
    }
</script>
