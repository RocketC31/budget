<script setup>
import { Link, Head } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import EmptyState from "@/Components/Partials/EmptyState";
import {Inertia} from "@inertiajs/inertia";

defineProps({
    tags: Array,
});

function remove(id) {
    if (confirm(trans('actions.confirm_action'))) {
        Inertia.delete(route('tags.delete', { tag : id }));
    }
}

</script>

<template>
    <Head :title="trans('models.tags')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.tags') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <Link :href="route('tags.create')" class="button">{{ trans('actions.create') }} {{ trans('models.tag') }}</Link>
                </div>
            </div>
            <div class="box">
                <template v-if="tags">
                    <div class="box__section box__section--header row">
                        <div class="row__column row__column--compact mr-2" style="width: 20px;"></div>
                        <div class="row__column">{{ trans('fields.name') }}</div>
                        <div class="row__column row__column--double" style="flex: 2;">{{ trans('models.spendings') }}</div>
                    </div>
                    <div v-for="tag in tags" class="box__section row">
                        <div class="row__column row__column--compact row__column--middle mr-2">
                            <div style="width: 15px; height: 15px; border-radius: 2px;" :style="'background: #'+tag.color+';'"></div>
                        </div>
                        <div class="row__column row__column--middle">{{ tag.name }}</div>
                        <div class="row__column row__column--middle">{{ tag.spendings.length }}</div>
                        <div class="row__column row__column--middle row row--right">
                            <div class="row__column row__column--compact">
                                <Link :href="route('tags.edit', { id: tag.id })">
                                    <i class="fas fa-pencil"></i>
                                </Link>
                            </div>
                            <div class="row__column row__column--compact ml-2">
                                <button v-if="tag.budgets.length === 0 && tag.spendings.length === 0" class="button link cursor-pointer" @click.stop="remove(tag.id)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <EmptyState v-else :payload="'tags'"></EmptyState>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
