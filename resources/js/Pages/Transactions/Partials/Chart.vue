<template>
    <div class="transaction-block" id="transaction-chart">
        <Chart :labels="labels" :data="transactionsData" :background-color="transactionsColor" :label="label" :type="'bar'"></Chart>
        <Chart :labels="labels" :data="transactionsData" :background-color="transactionsColor" :label="label" :type="'pie'"></Chart>
    </div>
</template>

<script>
    import Chart from "@/Components/Partials/Chart.vue";
    import { trans } from "matice";

    export default {
        components: {
            Chart
        },
        props: {
            transactionsChart: Object
        },

        setup(props) {
            const labels = [];
            const transactionsData = [];
            const transactionsColor = [];

            for (const [label, transaction] of Object.entries(props.transactionsChart)) {
                labels.push(label);
                transactionsData.push(transaction.amount);
                transactionsColor.push(transaction.color);
            }

            const label = trans('models.transactions');

            return { labels, transactionsColor, transactionsData, label }
        }
    }
</script>
