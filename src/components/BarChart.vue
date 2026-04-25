<script setup lang="ts">
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS, BarController, BarElement,
  CategoryScale, LinearScale, Tooltip, Legend,
} from 'chart.js';
import type { ChartData, ChartOptions, ChartDataset } from 'chart.js';

ChartJS.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const props = defineProps<{
  labels: string[];
  datasets: {
    label?: string;
    data: number[];
    backgroundColor?: string | string[];
    borderColor?: string | string[];
    borderRadius?: number;
    borderWidth?: number;
  }[];
  yTickPrefix?: string;
  yTickSuffix?: string;
  height?: number;
}>();

const chartData = computed<ChartData<'bar'>>(() => ({
  labels: props.labels,
  datasets: props.datasets as ChartDataset<'bar'>[],
}));

const chartOptions: ChartOptions<'bar'> = {
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
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>
