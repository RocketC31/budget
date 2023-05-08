<template>
    <Head :title="trans('actions.edit') + ' ' + trans('models.recurring')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2 class="mb-3">{{ trans('actions.edit') }} {{ trans('models.recurring') }}</h2>
            <form @submit.prevent="submit" autocomplete="off">
                <div class="input">
                    <label>{{ trans('fields.date') }}</label>
                    <DatePicker :start-date="form.date" @DateUpdated="onDateUpdate"></DatePicker>
                    <ValidationError v-if="errors.date" :message="errors.date"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('fields.description') }}</label>
                    <input type="text" name="description" v-model="form.description" />
                    <ValidationError v-if="errors.description" :message="errors.description"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('fields.amount') }}</label>
                    <input type="text" name="amount" v-model="form.amount" />
                    <ValidationError v-if="errors.amount" :message="errors.amount"></ValidationError>
                </div>
                <div class="input">
                    <Toggle @UpdateActive="onActiveSyncUpdate" :label="trans('fields.active')" :checked="form.active"></Toggle>
                </div>
                <div class="row">
                    <div class="row__column row__column--compact">
                        <button class="button">{{ trans('actions.edit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import {Head, useForm, usePage} from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import ValidationError from "@/Components/ValidationError.vue";
    import Toggle from "@/Components/Toggle.vue";
    import DatePicker from "@/Components/DatePicker.vue";
    import { computed } from "vue";

    const props = defineProps({
        recurring: Object
    });

    const patchMethodAvailable = computed(() => usePage().props.value.patchMethodAvailable);

    function onActiveSyncUpdate(isActive) {
        form.active = (isActive) ? 1 : 0;
    }

    function submit() {
        if (patchMethodAvailable.value) {
            form.patch(route('recurrings.update', { recurring: props.recurring.id }));
        } else {
            form.put(route('recurrings.update', { recurring: props.recurring.id }));
        }
    }

    const form = useForm({
        date: new Date(props.recurring.last_used_on).toISOString().slice(0, 10),
        description: props.recurring.description,
        amount: props.recurring.formatted_amount,
        active: props.recurring.active
    });

    function onDateUpdate(date) {
        form.date = date;
    }

    const errors = computed(() => usePage().props.value.errors);
</script>
