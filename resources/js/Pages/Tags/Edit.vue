<template>
    <Head :title="trans('actions.edit') + ' ' + trans('models.tag')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('actions.edit') }} {{ trans('models.tag') }}</h2>
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
                            <ColorPicker @ColorUpdated="onColorUpdate" :initial-color="tag.color"></ColorPicker>
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

<script setup>
    import { trans } from 'matice';
    import { Head, usePage, Link } from '@inertiajs/inertia-vue3';
    import { ref, computed } from "vue";
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import ValidationError from "@/Components/ValidationError";
    import ColorPicker from "@/Components/ColorPicker";
    import { Inertia } from "@inertiajs/inertia";

    const props = defineProps({
        tag: Object,
    });

    const tag = ref(props.tag);

    function submit() {
        tag.value.color = tag.value.color.replace("#", "");
        if (patchMethodAvailable.value)
            Inertia.patch(route('tags.update', { tag: tag.value.id }), tag.value);
        else
            Inertia.put(route('tags.update', { tag: tag.value.id }), tag.value);
    }

    function onColorUpdate(color) {
        tag.value.color = color;
    }

    const errors = computed(() => usePage().props.value.errors);
    const patchMethodAvailable = computed(() => usePage().props.value.patchMethodAvailable);
</script>
