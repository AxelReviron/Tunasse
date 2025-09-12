<script setup lang="ts">
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS, Title, Tooltip, Legend,
    LineElement, PointElement, CategoryScale, LinearScale,
    type ChartData, type ChartOptions
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps<{
    chartData: ChartData;
    chartOptions: ChartOptions;
}>();

</script>

<template>
    <Line
        id="transactions-line-chart"
        :options="props.chartOptions"
        :data="props.chartData"
    />
</template>
