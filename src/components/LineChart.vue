<script setup lang="ts">
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS, LineController, LineElement, PointElement,
  CategoryScale, LinearScale, Tooltip, Legend, Filler,
} from 'chart.js';
import type { ChartData, ChartOptions, ChartDataset } from 'chart.js';

ChartJS.register(LineController, LineElement, PointElement, CategoryScale, LinearScale, Tooltip, Legend, Filler);

const props = defineProps<{
  labels: string[];
  datasets: {
    label?: string;
    data: number[];
    borderColor?: string;
    backgroundColor?: string;
    fill?: boolean;
    tension?: number;
    pointRadius?: number;
    pointBackgroundColor?: string;
  }[];
  yTickPrefix?: string;
  yTickSuffix?: string;
  height?: number;
}>();

const chartData = computed<ChartData<'line'>>(() => ({
  labels: props.labels,
  datasets: props.datasets as ChartDataset<'line'>[],
}));

const chartOptions: ChartOptions<'line'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { mode: 'index' },
  },
  scales: {
    x: { grid: { display: false } },
    y: {
      grid: { color: 'rgba(0,0,0,0.06)' },
      ticks: {
        callback: (v) => `${props.yTickPrefix ?? ''}${v}${props.yTickSuffix ?? ''}`,
      },
    },
  },
};
</script>

<template>
  <div :style="{ height: (height ?? 200) + 'px' }">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>
