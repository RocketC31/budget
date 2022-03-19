<script setup>
import { trans } from 'matice';
import { Head } from '@inertiajs/inertia-vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

defineProps({

});

</script>

<template>
    <Head :title="trans('models.transactions')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row mb-3">
                <div class="row__column row__column--middle">
                    <h2>{{ trans('models.transactions') }}</h2>
                </div>
                <div class="row__column row__column--compact row__column--middle">
                    <a href="{{ trans('transactions.create') }}" class="button">{{ trans('actions.create') }} {{ trans('models.transactions') }}</a>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
