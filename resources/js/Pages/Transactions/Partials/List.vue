<template>
    <div class="box transaction-block overflow-auto" id="transaction-list">
        <table class="w-full whitespace-nowrap">
            <tbody>
            <template v-for="transaction in transactions">
                <Row :transaction="transaction" :currency="currency" :tags="tags"></Row>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script setup>
    import Row from "@/Pages/Transactions/Partials/Row";

    defineProps({
        transactions: Array,
        tags: Array,
        currency: String
    });
</script>
