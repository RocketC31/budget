<script setup>
import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import Layout from '@/Pages/Settings/Layout.vue';
import { computed, onMounted } from "vue";
import ValidationError from "@/Components/ValidationError";
import Searchable from "@/Components/Searchable";

defineProps({
    languages: Array
});

function submit() {
    form.post(route('settings.preferences'), {
        onSuccess: function onSuccess() {
            let bodyClass = document.getElementById("body").classList;
            bodyClass.remove('theme-light', 'theme-dark');
            bodyClass.add('theme-'+form.data().theme);

            let htmlClass = document.getElementsByTagName("html")[0].classList;
            htmlClass.remove('light', 'dark');
            htmlClass.add(form.data().theme);
        }
    })
}

const user = computed(() => usePage().props.value.auth.user);
const errors = computed(() => usePage().props.value.errors);

const form = useForm({
    theme: null,
    language: null,
    weekly_report: null,
    default_transaction_type: null,
    first_day_of_week: null
})

function languageUpdated(payload) {
    form.language = payload.key
}

onMounted(() => {
    form.theme = user.value.theme;
    form.language = user.value.language;
    form.weekly_report = user.value.weekly_report;
    form.default_transaction_type = user.value.default_transaction_type;
    form.first_day_of_week = user.value.first_day_of_week;
})

</script>

<template>
    <Head :title="trans('general.preferences')" />

    <Layout>
        <form @submit.prevent="submit">
            <div class="box">
                <div class="box__section">
                    <div class="input input--small">
                        <label>{{ trans('fields.language') }}</label>
                        <Searchable :name="'language'" :items="languages" :initial="user.language" @SelectUpdated="languageUpdated"></Searchable>
                        <ValidationError v-if="errors.language" :message="errors.language"></ValidationError>
                    </div>
                    <div class="input input--small">
                        <label>{{ trans('fields.theme') }}</label>
                        <select class="p-2.5" name="theme" v-model="form.theme">
                            <option value="light" :selected="user.theme === 'light'">Light</option>
                            <option value="dark" :selected="user.theme === 'dark'">Dark (Experimental)</option>
                        </select>
                        <ValidationError v-if="errors.language" :message="errors.theme"></ValidationError>
                    </div>
                    <div class="input input--small">
                        <label>{{ trans('fields.weekly_report') }}</label>
                        <div>
                            <input type="radio" name="weekly_report" value="1" v-model="form.weekly_report" /> {{ trans('actions.yes') }}
                        </div>
                        <div>
                            <input type="radio" name="weekly_report" value="0" v-model="form.weekly_report" /> {{ trans('actions.no') }}
                        </div>
                        <ValidationError v-if="errors.weekly_report" :message="errors.weekly_report"></ValidationError>
                    </div>
                    <div class="input input--small">
                        <label>{{ trans('fields.default_transaction_type') }}</label>
                        <div>
                            <input type="radio" name="default_transaction_type" value="earning" v-model="form.default_transaction_type"/> {{ trans('models.earning') }}
                        </div>
                        <div>
                            <input type="radio" name="default_transaction_type" value="spending" v-model="form.default_transaction_type"/> {{ trans('models.spending') }}
                        </div>
                        <ValidationError v-if="errors.default_transaction_type" :message="errors.default_transaction_type"></ValidationError>
                    </div>
                    <div class="input input--small">
                        <label>{{ trans('fields.first_day_of_week') }}</label>
                        <div>
                            <input type="radio" name="first_day_of_week" value="sunday" v-model="form.first_day_of_week"/> {{ trans('calendar.weekdays.6') }}
                        </div>
                        <div>
                            <input type="radio" name="first_day_of_week" value="monday" v-model="form.first_day_of_week"/> {{ trans('calendar.weekdays.0') }}
                        </div>
                        <ValidationError v-if="errors.first_day_of_week" :message="errors.first_day_of_week"></ValidationError>
                    </div>
                    <button class="button">{{ trans('actions.save') }}</button>
                </div>
            </div>
        </form>
    </Layout>
</template>
