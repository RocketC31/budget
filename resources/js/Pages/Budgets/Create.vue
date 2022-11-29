<template>
    <Head :title="trans('actions.create') + ' ' + trans('models.budget')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('actions.create') }} {{ trans('models.budget') }}</h2>
            <div class="box mt-3">
                <form @submit.prevent="submit">
                    <div class="box__section">
                        <div v-if="flash.message" class="mb-2 text-red-300">{{ flash.message }}</div>
                        <div class="input">
                            <label>{{ trans('models.tag') }}</label>
                            <select class="p-2.5" name="tag_id" v-model="budget.tag_id">
                                <option v-for="tag in tags" :value="tag.id">{{ tag.name }}</option>
                            </select>
                            <ValidationError :message="errors.tag_id" v-if="errors.tag_id"></ValidationError>
                        </div>
                        <div class="input">
                            <label>{{ trans('fields.period') }}</label>
                            <select class="p-2.5" name="period" v-model="budget.period">
                                <option value="yearly">{{ trans('calendar.intervals.yearly') }}</option>
                                <option value="monthly" selected>{{ trans('calendar.intervals.monthly') }}</option>
                                <option value="weekly">{{ trans('calendar.intervals.weekly') }}</option>
                                <option value="daily">{{ trans('calendar.intervals.daily') }}</option>
                            </select>
                            <ValidationError v-if="errors.period" :message="errors.period"></ValidationError>
                        </div>
                        <div class="input">
                            <label>{{ trans('fields.amount') }}</label>
                            <input type="text" name="amount" v-model="budget.amount"/>
                            <ValidationError v-if="errors.amount" :message="errors.amount"></ValidationError>
                        </div>
                    </div>
                    <div class="box__section box__section--highlight row row--right">
                        <div class="row__column row__column--compact row__column--middle">
                            <Link :href="route('budgets.index')">{{ trans('actions.cancel') }}</Link>
                        </div>
                        <div class="row__column row__column--compact ml-2">
                            <button class="button">{{ trans('actions.create') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { trans } from 'matice';
    import { Head, usePage, Link } from '@inertiajs/inertia-vue3';
    import { ref, computed } from "vue";
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import ValidationError from "@/Components/ValidationError";
    import { Inertia } from "@inertiajs/inertia";

    defineProps({
        tags: Array,
    });

    const budget = ref({
        tag_id: null,
        period: "monthly",
        amount: null,
    });

    const errors = computed(() => usePage().props.value.errors);
    const flash = computed(() => usePage().props.value.flash);

    function submit() {
        Inertia.post(route('budgets.store'), budget.value);
    }
</script>
