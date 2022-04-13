<template>
    <Head :title="trans('general.account')" />

    <Layout>
        <form @submit.prevent="submit">
            <div class="box">
                <div class="box__section">
                    <div class="input input--small">
                        <label>{{ trans('fields.email') }}</label>
                        <input type="email" name="email" v-model="form.email" />
                    </div>
                    <div class="row">
                        <div class="row__column input">
                            <label>{{ trans('fields.password') }}</label>
                            <input type="password" name="password" v-model="form.password"/>
                            <ValidationError v-if="errors.password" :message="errors.password"></ValidationError>
                        </div>
                        <div class="row__column input ml-2">
                            <label>{{ trans('actions.verify') }} {{ trans('fields.password') }}</label>
                            <input type="password" name="password_confirmation" v-model="form.password_confirmation"/>
                            <ValidationError v-if="errors.password_confirmation" :message="errors.password_confirmation"></ValidationError>
                        </div>
                    </div>
                    <button class="button">{{ trans('actions.save') }}</button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
    import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import Layout from '@/Pages/Settings/Layout.vue';
    import { computed, onMounted } from "vue";
    import ValidationError from "@/Components/ValidationError";

    function submit() {
        form.post(route('settings.profile'));
    }

    const user = computed(() => usePage().props.value.auth.user);
    const errors = computed(() => usePage().props.value.errors);

    const form = useForm({
        email: null,
        password: null,
        password_confirmation: null
    });

    onMounted(() => {
        form.email = user.value.email;
    });
</script>
