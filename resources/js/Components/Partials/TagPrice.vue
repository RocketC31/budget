<template>
    <div class="box mt-3">
        <div v-for="tag in tagsPrice" class="box__section row row--seperate">
            <div class="row__column row__column--middle color-dark">
                <Tag :tag="tag"></Tag>
            </div>
            <template v-if="totalSpent">
                <div class="row__column row__column--middle">
                    <progress :max="totalSpent" :value="tag.amount"></progress>
                </div>
                <div class="row__column row__column--middle text-right"><span v-html="currency"></span> {{ formattedAmount(tag.amount) }} / <span v-html="currency"></span> {{ ormattedAmount(totalSpent) }}</div>
            </template>
            <template v-else>
                <div class="row__column row__column--middle text-right"><span v-html="currency"></span> {{ formattedAmount(tag.amount) }}</div>
            </template>
        </div>
    </div>
</template>

<script setup>
    import { formattedAmount } from '@/tools';
    import Tag from "@/Components/Partials/Tag.vue";

    defineProps({
        tagsPrice: Array,
        totalSpent: {
            type: String,
            default: null
        },
        currency: String
    });
</script>
