<script setup lang="ts">
import { Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS, Title, Tooltip, Legend,
    ArcElement, type ChartData, type ChartOptions
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps<{
    chartData: ChartData;
    chartOptions: ChartOptions;
}>();

</script>

<template>
    <Doughnut
        :data="props.chartData"
        :options="props.chartOptions"
    />
</template>
