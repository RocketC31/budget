<template>
    <Head :title="trans('actions.edit') + ' ' + trans(title)" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('actions.edit') }} {{ trans(title) }}</h2>
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

<script>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head, useForm, usePage, Link } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import DatePicker from "@/Components/DatePicker.vue";
    import ValidationError from "@/Components/ValidationError.vue";
    import Searchable from "@/Components/Searchable.vue";
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
            transaction: Object,
            tags: Array
        },

        setup(props) {
            const errors = computed(() => usePage().props.value.errors);
            const patchMethodAvailable = computed(() => usePage().props.value.patchMethodAvailable);
            const title = "models." + props.transaction.type;
            const form = useForm({
                tag_id: props.transaction.tag_id,
                date: new Date(props.transaction.happened_on).toISOString().slice(0, 10),
                description: props.transaction.description,
                amount: props.transaction.formatted_amount
            });
            function submit() {
                if (patchMethodAvailable.value) {
                    form.patch(route('transactions.update', { transaction: props.transaction.id }));
                } else {
                    form.put(route('transactions.update', { transaction: props.transaction.id }));
                }
            }
            function onDateUpdate(date) {
                form.date = date;
            }
            return {
                title,
                trans,
                errors,
                form,
                submit,
                onDateUpdate
            }
        },
    }
</script>
