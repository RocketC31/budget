<script setup>
//TODO : maybe we can remove it because recurring is set in transactions page now !
// And this component dont add anyting
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import ValidationError from "@/Components/ValidationError";
import DatePicker from "@/Components/DatePicker";
import Searchable from "@/Components/Searchable";
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";

defineProps({
    tags: Array
});

const recurring = ref({
    day: null,
    end: new Date().toISOString().slice(0, 10),
    description: null,
    type: 'spending',
    amount: null
});

let loading = ref(false);
let tag = ref("");

function submit() {
    if (!loading.value) {
        loading.value = true;
        let body = recurring.value;
        body["tag"] = tag.value;
        Inertia.post('/recurrings', body, {
            onFinish: () => loading.value = false
        })
    }
}

function tagUpdated(payload) {
    tag.value = payload.key;
}

const errors = computed(() => usePage().props.value.errors);

</script>
<template>
    <Head :title="trans('actions.create') + ' ' + trans('models.recurring')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2 class="mb-3">{{ trans('actions.create') }} {{ trans('models.recurring') }}</h2>
            <form @submit.prevent="submit" autocomplete="off">
                <div class="input">
                    <label>{{ trans('calendar.day') }}</label>
                    <input type="text" name="day" v-model="recurring.day" />
                    <div style="font-weight: 700; font-size: 14px; margin-top: 5px;">1 - 28</div>
                    <ValidationError v-if="errors.day" :message="errors.day"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('general.end') }}</label>
                    <DatePicker name="end"></DatePicker>
                    <ValidationError v-if="errors.end" :message="errors.end"></ValidationError>
                    <input type="checkbox" name="end" id="endForever" v-model="recurring.end" />
                    <label for="endForever">{{ trans('general.forever') }}</label>
                </div>
                <div class="input">
                    <label>{{ trans('models.tag') }}</label>
                    <Searchable name="tag" :items="tags" @SelectUpdated="tagUpdated"></Searchable>
                    <ValidationError v-if="errors.tag" :message="errors.tag"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('fields.description') }}</label>
                    <input type="text" name="description" v-model="recurring.description" />
                    <ValidationError v-if="errors.description" :message="errors.description"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('fields.amount') }}</label>
                    <input type="text" name="amount" v-model="recurring.amount" />
                    <ValidationError v-if="errors.amount" :message="errors.amount"></ValidationError>
                </div>
                <div class="row">
                    <div class="row__column row__column--compact">
                        <button class="button">{{ trans('actions.create') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </BreezeAuthenticatedLayout>
</template>
