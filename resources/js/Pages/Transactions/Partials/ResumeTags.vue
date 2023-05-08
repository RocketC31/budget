<template>
    <div class="transaction-block" id="transaction-tags">
        <TagPrice :tags-price="tagsPrice" :currency="currency" :total-spent="totalSpent"></TagPrice>
    </div>
</template>

<script setup>
    import TagPrice from "@/Components/Partials/TagPrice.vue";

    defineProps({
        tagsPrice: Array,
        totalSpent: {
            type: String,
            default: null
        },
        currency: String
    });
</script>
