<script setup>
import TagPrice from "@/Components/Partials/TagPrice";

defineProps({
    tagsPrice: Array,
    totalSpent: {
        type: String,
        default: null
    },
    currency: String
});

</script>
<template>
    <div class="transaction-block" id="transaction-tags">
        <TagPrice :tags-price="tagsPrice" :currency="currency" :total-spent="totalSpent"></TagPrice>
    </div>
</template>
