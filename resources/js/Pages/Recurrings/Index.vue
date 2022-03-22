<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Link, Head } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import { formattedAmount } from '@/tools';
import Tag from "@/Components/Partials/Tag";
import EmptyState from "@/Components/Partials/EmptyState";

defineProps({
    recurrings: Array,
    currency: String
});
</script>
<template>
    <Head :title="trans('models.recurrings')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.recurrings') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <Link :href="route('recurrings.create')" class="button">{{ trans('actions.create') }} {{ trans('models.recurring') }}</Link>
                </div>
            </div>
            <div class="box mt-3">
                <div v-for="recurring in recurrings" class="box__section row">
                    <div class="row__column">
                        <div class="color-dark">
                            <Link :href="route('recurrings.show', { id: recurring.id })">{{ recurring.description }}</Link>
                        </div>
                        <div class="row mt-1">
                            <div class="row__column row__column--compact" style="font-size: 14px; font-weight: 600;"><span v-html="currency"></span> {{ formattedAmount(recurring.amount) }}</div>
                            <div v-if="recurring.due_days" class="row__column row__column--compact ml-2" style="font-size: 14px; font-weight: 600;"><i class="fas fa-clock"></i> {{ trans('activities.recurring.due_in', { args: { due: recurring.due_days } }) }}</div>
                        </div>
                    </div>
                    <div class="row__column row__column--middle">
                        <span v-if="recurring.status" style="border-radius: 5px; background: #D8F9E8; color: #51B07F; padding: 5px 10px; font-size: 14px; font-weight: 600;"><i class="fas fa-check fa-xs"></i> {{ trans('general.active') }}</span>
                        <span v-else style="border-radius: 5px; background: #FFE7EC; color: #F25C68; padding: 5px 10px; font-size: 14px; font-weight: 600;"><i class="fas fa-times fa-xs"></i> {{ trans('general.inactive') }}</span>
                    </div>
                    <div class="row__column row__column--middle">
                        <Tag v-if="recurring.tag" :tag="recurring.tag"></Tag>
                    </div>
                </div>
                <EmptyState v-if="recurrings.length < 1" :payload="'recurrings'"></EmptyState>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
