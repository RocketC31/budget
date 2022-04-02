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
        spending: Object,
        tags: Array
    },

    setup(props) {
        const errors = computed(() => usePage().props.value.errors);
        const patchMethodAvailable = computed(() => usePage().props.value.patchMethodAvailable);
        const form = useForm({
            tag_id: props.spending.tag_id,
            date: new Date(props.spending.happened_on).toISOString().slice(0, 10),
            description: props.spending.description,
            amount: props.spending.formatted_amount
        });
        function submit() {
            let updateRecurring = false;
            if (props.spending.recurring_id !== null) {
                if (confirm(trans("models.recurring_attached_confirm"))) {
                    updateRecurring = true;
                }
            }
            if (patchMethodAvailable.value) {
                form.patch(route('spendings.update', { spending: props.spending.id, recurring_update: updateRecurring }));
            } else {
                form.put(route('spendings.update', { spending: props.spending.id, recurring_update: updateRecurring }));
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
<template>
    <Head :title="trans('models.spending')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('actions.edit') }} {{ trans('models.spending') }}</h2>
            <div class="box mt-3">
                <form @submit.prevent="submit" autocomplete="off">
                    <div class="box__section">
                        <div class="input">
                            <label>{{ trans('models.tag') }}</label>
                            <select class="p-2.5" name="tag_id" v-model="form.tag_id" >
                                <option value="">-</option>
                                <option v-for="tag in tags" :value="tag.id">{{ tag.name }}</option>
                            </select>
                            <ValidationError v-if="errors.tag_id" :message="errors.tag_id"></ValidationError>
                        </div>
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
