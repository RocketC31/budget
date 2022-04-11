<script setup>
import { Link, Head } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
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
                                    <td class="px-1 w-9/12">
                                        <div class="flex items-center dark:text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M9.16667 2.5L16.6667 10C17.0911 10.4745 17.0911 11.1922 16.6667 11.6667L11.6667 16.6667C11.1922 17.0911 10.4745 17.0911 10 16.6667L2.5 9.16667V5.83333C2.5 3.99238 3.99238 2.5 5.83333 2.5H9.16667" :stroke="'#'+ tag.color" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <circle cx="7.50004" cy="7.49967" r="1.66667" :stroke="'#'+ tag.color" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></circle>
                                            </svg>
                                            <p class="ml-1">{{ tag.name }}</p>
                                        </div>
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
                <div v-else class="box__section text-center">
                    <div class="mb-1">{{ trans('general.empty_trash') }}</div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
