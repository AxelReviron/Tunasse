<script setup lang="ts">
import { computed } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import type { ChartData, ChartOptions } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps<{
  labels: string[];
  data: number[];
  colors: string[];
  height?: number;
}>();

const chartData = computed<ChartData<'pie', number[], string>>(() => ({
  labels: props.labels,
  datasets: [{
    data:            props.data,
    backgroundColor: props.colors.map(c => c + 'CC'),
    borderColor:     props.colors,
    borderWidth:     1.5,
  }],
}));

const chartOptions: ChartOptions<'pie'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true, position: 'right', labels: { padding: 24 } },
    tooltip: { mode: 'index' },
  },
};
</script>

<template>
  <div :style="{ height: (height ?? 260) + 'px' }">
    <Pie :data="chartData" :options="chartOptions" />
  </div>
</template>
