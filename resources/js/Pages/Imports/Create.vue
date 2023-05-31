<template>
    <Head :title="trans('actions.create') + ' ' + trans('models.import')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('actions.create') }} {{ trans('models.import') }}</h2>
            <div class="box mt-3">
                <form @submit.prevent="submit">
                    <div class="box__section">
                        <div class="input">
                            <label>{{ trans('fields.name') }}</label>
                            <input type="text" name="name" v-model="form.name" />
                            <ValidationError v-if="errors.name" :message="errors.name"></ValidationError>
                        </div>
                        <div class="input input--small mb-0">
                            <label>{{ trans('fields.file') }}</label>
                            <input type="file" name="file" @input="form.file = $event.target.files[0]" />
                            <ValidationError v-if="errors.file" :message="errors.file"></ValidationError>
                        </div>
                    </div>
                    <div class="box__section box__section--highlight row row--right">
                        <div class="row__column row__column--compact row__column--middle">
                            <Link :href="route('imports.index')">{{ trans('actions.cancel') }}</Link>
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
    import {Link, Head, useForm, usePage} from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import ValidationError from "@/Components/ValidationError.vue";
    import { computed } from "vue";

    const form = useForm({
        name: null,
        file: null
    });

    function submit() {
        form.post(route('imports.store'), {
            file: form.file,
            forceFormData: true
        })
    }

    const errors = computed(() => usePage().props.value.errors);
</script>
