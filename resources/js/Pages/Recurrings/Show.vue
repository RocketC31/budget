<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import Tag from "@/Components/Partials/Tag";

defineProps({
    recurring: Object
});
</script>
<template>
    <Head :title="trans('models.recurrings')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ recurring.description }}</h2>
            <div class="row my-3">
                <div class="row__column row__column--compact">
                    <span v-if="recurring.status" style="border-radius: 5px; background: #D8F9E8; color: #51B07F; padding: 5px 10px; font-size: 14px; font-weight: 600;"><i class="fas fa-check fa-xs"></i> {{ trans('general.active') }}</span>
                    <span v-else style="border-radius: 5px; background: #FFE7EC; color: #F25C68; padding: 5px 10px; font-size: 14px; font-weight: 600;"><i class="fas fa-times fa-xs"></i> {{ trans('general.inactive') }}</span>
                </div>
                <div v-if="recurring.tag" class="row__column row__column--compact ml-1">
                    <Tag :tag="recurring.tag"></Tag>
                </div>
            </div>
            <div class="color-dark mb-2">{{ trans('models.spendings') }}</div>
            <div class="box">
                <template v-if="recurring.spendings">
                    <div v-for="spending in recurring.spendings" class="box__section">
                        <div>
                            <div class="color-dark">{{ spending.happened_on }}</div>
                            <div class="mt-1" style="font-size: 14px; font-weight: 600;">{{ spending.formatted_happened_on }}</div>
                        </div>
                        <div></div>
                    </div>
                </template>
                <div v-else class="box__section text-center">{{ trans('activities.spending.no_spendings') }}</div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
