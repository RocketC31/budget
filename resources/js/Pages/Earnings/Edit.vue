<template>
    <Head :title="trans('models.earning')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('actions.edit') }} {{ trans('models.earning') }}</h2>
            <div class="box mt-3">
                <form @submit.prevent="submit" autocomplete="off">
                    <div class="box__section">
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
                        <div class="input mb-0">
                            <label>{{ trans('fields.amount') }}</label>
                            <input type="text" name="amount" v-model="form.amount" />
                            <ValidationError v-if="errors.amount" :message="errors.amount"></ValidationError>
                        </div>
                    </div>
                    <div class="box__section box__section--highlight row row--right">
                        <div class="row__column row__column--compact row__column--middle">
                            <Link :href="route('transactions.index')">{{ trans('actions.cancel') }}</Link>
                        </div>
                        <div class="row__column row__column--compact ml-2">
                            <button class="button">{{ trans('actions.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head, useForm, usePage, Link } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import DatePicker from "@/Components/DatePicker";
    import ValidationError from "@/Components/ValidationError";
    import Searchable from "@/Components/Searchable";
    import { computed } from "vue";

    export default {
        components: {
            DatePicker,
            Link,
            BreezeAuthenticatedLayout,
            ValidationError,
            Head,
            Searchable
        },

        props: {
            earning: Object,
            tags: Array
        },

        setup(props) {
            const errors = computed(() => usePage().props.value.errors);
            const patchMethodAvailable = computed(() => usePage().props.value.patchMethodAvailable);
            const form = useForm({
                date: new Date(props.earning.happened_on).toISOString().slice(0, 10),
                description: props.earning.description,
                amount: props.earning.formatted_amount
            });
            function submit() {
                if (patchMethodAvailable.value) {
                    form.patch(route('earnings.update', { earning: props.earning.id }));
                } else {
                    form.put(route('earnings.update', { earning: props.earning.id }));
                }
            }
            function onDateUpdate(date) {
                form.date = date;
            }
            return {
                trans,
                errors,
                form,
                submit,
                onDateUpdate
            }
        },
    }
</script>
