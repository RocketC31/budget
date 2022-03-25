<script>
import { v4 as uuidv4 } from 'uuid';
import Chart from 'chart.js/auto';
import { onMounted } from 'vue';

export default {
    props: {
        type: {
            type: String,
            default: 'line'
        },
        data: {
            type: Object
        },
        labels: {
            type: Array
        },
        label: {
            type: String,
            default: "My dataset"
        },
        additionalClass: {
            type: String,
            default: ''
        },
        backgroundColor: {
            type: Array,
            default: []
        },
        borderColor: {
            type: String,
            default: 'rgb(75, 192, 192)'
        },
        hoverOffset: {
            type: Number,
            default: 1
        }
    },

    setup(props) {
        const unique_id = uuidv4();

        const datasets = [{
            data: props.data,
            label: props.label,
            backgroundColor: props.backgroundColor,
            borderColor: props.borderColor,
            hoverOffset: props.hoverOffset
        }];

        onMounted(() => {
            const canvas = document.getElementById(unique_id).getContext('2d');
            new Chart(canvas, {
                    type: props.type,
                    data: {
                        labels: props.labels,
                        datasets: datasets,
                    }
                }
            );
        });

        return { unique_id }
    },
}

</script>
<template>
    <div :class="[additionalClass, { circle : type === 'pie' }]">
        <canvas :id="unique_id"></canvas>
    </div>
</template>
<style scoped>
    .circle {
        max-width: 70%;
        margin:auto;
    }
</style>
