<template>
    <Head :title=" trans('actions.create') + ' ' + trans('models.space')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2 class="mb-3">{{ trans('actions.create') }} {{ trans('models.space') }}</h2>
            <div class="box mt-3">
                <form @submit.prevent="submit" autocomplete="off">
                    <div class="box__section">
                        <div class="input">
                            <label>{{ trans('fields.name') }}</label>
                            <input type="text" name="name" v-model="space.name" />
                            <ValidationError v-if="errors.name" :message="errors.name"></ValidationError>
                        </div>
                        <div class="input mb-0">
                            <label>{{ trans('fields.currency') }}</label>
                            <select class="p-2.5" name="currency_id" v-model="space.currency_id">
                                <option v-for="currency in currencies" :value="currency.id">{{ currency.name }}</option>
                            </select>
                            <ValidationError v-if="errors.currency_id" :message="errors.currency_id"></ValidationError>
                        </div>
                    </div>
                    <div class="box__section box__section--highlight row row--right">
                        <div class="row__column row__column--compact row__column--middle">
                            <Link :href="route('settings.spaces.index')">{{ trans('actions.cancel') }}</Link>
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
    import ColorPicker from "@/Components/ColorPicker";
    import { Inertia } from "@inertiajs/inertia";

    defineProps({
        currencies: Array
    });

    const space = ref({
        name: null,
        currency_id: 1,
    });

    function submit() {
        Inertia.post(route('spaces.store'), space.value);
    }

    const errors = computed(() => usePage().props.value.errors);
</script>
