<script setup>
import { trans } from 'matice';
import { Head, usePage, Link } from '@inertiajs/inertia-vue3';
import { ref, computed } from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import ValidationError from "@/Components/ValidationError";
import ColorPicker from "@/Components/ColorPicker";
import { Inertia } from "@inertiajs/inertia";

const tag = ref({
    name: "",
    color: "",
});

function submit() {
    tag.value.color = tag.value.color.replace("#", "");
    Inertia.post(route('tags.store'), tag.value);
}

function onColorUpdate(color) {
    tag.value.color = color;
}

const errors = computed(() => usePage().props.value.errors);

</script>

<template>
    <Head :title="trans('models.transactions')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('actions.create') }} {{ trans('models.tag') }}</h2>
            <div class="box mt-3">
                <form @submit.prevent="submit" autocomplete="off">
                    <div class="box__section">
                        <div class="input">
                            <label>{{ trans('fields.name') }}</label>
                            <input type="text" name="name" v-model="tag.name" />
                            <ValidationError v-if="errors.name" :message="errors.name"></ValidationError>
                        </div>
                        <div class="input input--small mb-0">
                            <label>{{ trans('fields.color') }}</label>
                            <ColorPicker @ColorUpdated="onColorUpdate"></ColorPicker>
                            <ValidationError v-if="errors.color" :message="errors.color"></ValidationError>
                        </div>
                    </div>
                    <div class="box__section box__section--highlight row row--right">
                        <div class="row__column row__column--compact row__column--middle">
                            <Link :href="route('tags.index')">{{ trans('actions.cancel') }}</Link>
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
