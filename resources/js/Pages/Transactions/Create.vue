<script setup>
import { trans } from 'matice';
import { Head } from '@inertiajs/inertia-vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

defineProps({
    tags: Array,
    currencies: Array,
    defaultTransactionType: String,
    firstDayOfWeek: Number,
    defaultCurrencyId: Number,
    recurringsIntervals: Array
});

</script>

<template>
    <Head :title="trans('models.transactions')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper mw-400 my-3">
            <h2 class="mb-3">{{ trans('actions.create') }} {{ trans('models.transaction') }}</h2>
        </div>
    </BreezeAuthenticatedLayout>
</template>
