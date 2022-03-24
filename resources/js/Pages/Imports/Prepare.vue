<script setup>
import { Head, useForm, usePage } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { computed } from "vue";

const props = defineProps({
    headers: Array,
    import: Object
});

const form = useForm({
    column_happened_on: null,
    column_description: null,
    column_amount: null
});

function submit() {
    form.post(route('imports.prepare.store', { import: props.import.id}))
}

const errors = computed(() => usePage().props.value.errors);

</script>

<template>
    <Head :title="trans('actions.prepare') + ' ' + trans('models.imports')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2 class="mb-3">{{ trans('actions.prepare') }} {{ trans('models.import') }}</h2>
            <div class="box">
                <form @submit.prevent="submit">
                    <div class="box__section">
                        <div class="row row--gutter">
                            <div class="row__column">
                                <div class="input mb-0">
                                    <label>{{ trans('fields.date') }}</label>
                                    <select name="column_happened_on" v-model="form.column_happened_on">
                                        <option v-for="(header,index) in headers" :value="index">{{ header }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row__column">
                                <div class="input mb-0">
                                    <label>{{ trans('fields.description') }}</label>
                                    <select name="column_description" v-model="form.column_description">
                                        <option v-for="(header,index) in headers" :value="index">{{ header }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row__column">
                                <div class="input mb-0">
                                    <label>{{ trans('fields.amount') }}</label>
                                    <select name="column_amount"  v-model="form.column_amount">
                                        <option v-for="(header,index) in headers" :value="index">{{ header }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box__section box__section--highlight text-right">
                        <button class="button">{{ trans('actions.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
