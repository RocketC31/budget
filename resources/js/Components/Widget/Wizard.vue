<script setup>
//TODO : not make widget so abstract. Go to make dedicated component
import {ref} from "vue";
import { trans } from "matice";
import { onMounted, computed } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    types: {
        type: Array,
        default: []
    },
    expectedProperties: {
        type: Object,
        default: {}
    }
})

const visible = ref(false);

const form = useForm({
    type: null,
    providedProperties: {}
})

function submit() {
    form.post(route('widgets.store'), {
        onFinish: function onFinish() {
            visible.value = false;
        }
    });
}

function toggle() {
    visible.value = !visible.value;
}

function refreshProvidedProperties() {
    form.providedProperties = {};
    let count = countExpectedProperties.value;
    if (count !== null) {
        for (const [key, value] of Object.entries(getExpectedProperties)) {
            form.providedProperties[key] = null;
        }
    }
}

const countExpectedProperties = computed(() => {
    if (props.expectedProperties[form.type].length > 0)
        return props.expectedProperties[form.type].length;
    if (typeof props.expectedProperties[form.type] === 'object'
        && Object.keys(props.expectedProperties[form.type]).length > 0)
        return Object.keys(props.expectedProperties[form.type]).length;
    return null;
});

const getExpectedProperties = computed(() => {
    if (countExpectedProperties)
        return props.expectedProperties[form.type];
    return null;
});

onMounted(() => {
    form.type = props.types[0];
    refreshProvidedProperties();
})

</script>
<template>
    <div>
        <div v-if="visible" class="box">
            <form @submit.prevent="submit">
                <div class="box__section">
                    <div class="input">
                        <label>{{ trans('fields.type') }}</label>
                        <select class="p-2.5" v-model="form.type" @change="refreshProvidedProperties">
                            <option v-for="type in types" :value="type">{{ trans('configuration.widget.'+type) }}</option>
                        </select>
                    </div>
                    <div v-if="getExpectedProperties" class="input">
                        <label>{{ trans('general.properties') }}</label>
                        <div v-for="(value, key) in getExpectedProperties">
                            <div class="mb-05">{{ trans('fields.'+key) }}</div>
                            <select class="p-2.5" v-model="form.providedProperties[key]">
                                <option v-for="(y,t) in value" :value="y">{{ trans(t) }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <button class="button mr-1" @click="persist">{{ trans('actions.create') }}</button>
                        <button class="button button--secondary" @click="toggle">{{ trans('actions.cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>
        <button v-else @click="toggle" class="button">{{ trans('actions.create') }}</button>
    </div>
</template>
