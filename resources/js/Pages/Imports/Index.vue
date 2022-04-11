<script setup>
import { Link, Head } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import EmptyState from "@/Components/Partials/EmptyState";
import { Inertia } from "@inertiajs/inertia";

defineProps({
    imports: Array
});

function remove(id) {
    if (confirm(trans('actions.confirm_action'))) {
        Inertia.delete(route('imports.delete', { import : id }));
    }
}

</script>

<template>
    <Head :title="trans('models.imports')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.imports') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <Link :href="route('imports.create')" class="button">{{ trans('actions.create') }} {{ trans('models.import') }}</Link>
                </div>
            </div>
            <div class="box" v-if="imports.length > 0" >
                <div class="box__section box__section--header row">
                    <div class="row__column">{{ trans('fields.name') }}</div>
                    <div class="row__column">{{ trans('fields.status') }}</div>
                    <div class="row__column row__column--compact" style="width: 150px;"></div>
                </div>
                <div v-for="imp in imports" class="box__section row">
                    <div class="row__column">{{ imp.name }}</div>
                    <div class="row__column">{{ imp.status < 2 ? (imp.status + 1) + ' / 3' : trans('actions.completed') }}</div>
                    <div class="row__column row__column--compact text-right" style="width: 100px;">
                        <Link v-if="imp.status < 2 && imp.status == 0" :href="route('imports.prepare', { import: imp.id })">
                            {{ trans('actions.next') }}</Link>
                        <Link v-if="imp.status < 2 && imp.status != 0" :href="route('imports.complete', { import: imp.id })">
                            {{ trans('actions.next') }}</Link>
                    </div>
                    <div class="row__column row__column--compact text-right" style="width: 50px;">
                        <i v-if="imp.spendings.length > 0" class="fas fa-trash-alt"></i>
                        <button v-else @click="remove(imp.id)" class="button link">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <EmptyState v-else :payload="'imports'"></EmptyState>
        </div>
    </BreezeAuthenticatedLayout>
</template>
