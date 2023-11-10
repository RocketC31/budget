<template>
    <div class="box transaction-block overflow-auto" id="transaction-list">
        <table class="w-full whitespace-nowrap">
            <tbody>
                <template v-for="transaction in transactions" :key="`${transaction.index}`">
                    <Row :transaction="transaction" :currency="currency" :tags="tags" :data-type="dataType"></Row>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script setup>
    import Row from "@/Pages/Transactions/Partials/Row.vue";

    defineProps({
        transactions: Array,
        tags: Array,
        dataType: String,
        currency: String
    });
</script>
