<script setup>
import { Head, useForm, usePage} from "@inertiajs/inertia-vue3";
import { trans } from 'matice';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { computed } from "vue";
import ValidationError from "@/Components/ValidationError";

const props = defineProps({
    tags: Array,
    rows: Array,
    import: Object,
});

function submit() {
    form.post(route('imports.complete.store', { import: props.import.id } ))
}

function constructRowsFromProps() {
    const rows = [];
    props.rows.forEach((row) => {
       let r = row;
       r['selected'] = false;
       rows.push(r);
    });

    return rows;
}

const form = useForm({
    rows: constructRowsFromProps(),
    column_description: null,
    column_amount: null,
    date_format: 'Y-m-d'
});

const errors = computed(() => usePage().props.value.errors);

function selectAll() {
    form.rows.forEach((row) => {
        row.selected = true;
    })
}

function deselectAll() {
    form.rows.forEach((row) => {
        row.selected = false;
    })
}

</script>

<template>
    <Head :title="trans('actions.complete') + ' ' + trans('models.imports')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2 class="mb-3">{{ trans('actions.complete') }} {{ trans('models.import') }}</h2>
            <div class="box">
                <form @submit.prevent="submit">
                    <div class="box__section">
                        <div class="input mb-0">
                            <label>{{ trans('fields.date_format') }}</label>
                            <select name="date_format" v-model="form.date_format">
                                <option value="Y-m-d" :selected="form.date_format === 'Y-m-d'">YYYY-MM-DD</option>
                                <option value="Y/m/d" :selected="form.date_format === 'Y/m/d'">YYYY/MM/DD</option>
                                <option value="Ymd" :selected="form.date_format === 'Ymd'">YYYYMMDD</option>
                            </select>
                        </div>
                        <div class="row mt-2">
                            <button style="padding: 5px 10px;" class="btn btn-blue bg-blue-500 hover:bg-blue-700 text-white" @click.prevent.stop="selectAll" type="button">{{ trans('actions.select_all') }}</button>
                            <button style="padding: 5px 10px;" class="btn btn-blue bg-blue-500 hover:bg-blue-700 text-white ml-1" @click.prevent.stop="deselectAll" type="button">{{ trans('actions.deselect_all') }}</button>
                        </div>
                    </div>
                    <div class="box__section box__section--header">
                        <div class="row row--gutter">
                            <div class="row__column row__column--compact" style="width: 100px;">{{ trans('models.imports') }}</div>
                            <div class="row__column">{{ trans('models.tag') }}</div>
                            <div class="row__column">{{ trans('fields.date') }}</div>
                            <div class="row__column row__column--triple">{{ trans('fields.description') }}</div>
                            <div class="row__column">{{ trans('fields.amount') }}</div>
                        </div>
                    </div>
                    <div v-for="(row, index) in form.rows" class="box__section">
                        <div class="row row--gutter">
                            <div class="row__column row__column--compact row__column--middle" style="width: 100px;">
                                <input type="checkbox" :name="row['import']" v-model="form.rows[index]['selected']"/></div>
                            <div class="row__column">
                                <select :name="'rows['+index+'][tag_id]'" v-model="form.rows[index]['tag_id']">
                                    <option value="">-</option>
                                    <option v-for="tag in tags" :value="tag.id">{{ tag.name }}</option>
                                </select>
                                <ValidationError v-if="errors['rows.'+index+'.tag_id']" :message="errors['rows.'+index+'.tag_id']" ></ValidationError>
                            </div>
                            <div class="row__column">
                                <input type="text" :name="'rows['+index+'][happened_on]'" v-model="form.rows[index]['happened_on']" />
                                <ValidationError v-if="errors['rows.'+index+'.happened_on']" :message="errors['rows.'+index+'.happened_on']" ></ValidationError>
                            </div>
                            <div class="row__column row__column--triple">
                                <input type="text" :name="'rows['+index+'][description]'" v-model="form.rows[index]['description']"/>
                                <ValidationError v-if="errors['rows.'+index+'.description']" :message="errors['rows.'+index+'.description']" ></ValidationError>
                            </div>
                            <div class="row__column">
                                <input type="text" :name="'rows['+index+'][amount]'" v-model="form.rows[index]['amount']"/>
                                <ValidationError v-if="errors['rows.'+index+'.amount']" :message="errors['rows.'+index+'.amount']" ></ValidationError>
                            </div>
                        </div>
                    </div>
                    <div class="box__section box__section--highlight text-right">
                        <button class="button">{{ trans('actions.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
