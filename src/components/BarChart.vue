<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import {
  Chart, BarController, BarElement,
  CategoryScale, LinearScale, Tooltip, Legend,
} from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

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

const canvas = ref<HTMLCanvasElement | null>(null);
let chart: Chart | null = null;

function buildChart() {
  if (!canvas.value) return;
  chart?.destroy();
  chart = new Chart(canvas.value, {
    type: 'bar',
    data: { labels: props.labels, datasets: props.datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { mode: 'index' } },
      scales: {
        x: { grid: { display: false } },
        y: {
          grid: { color: 'rgba(0,0,0,0.06)' },
          ticks: {
            callback: (v) => `${props.yTickPrefix ?? ''}${v}${props.yTickSuffix ?? ''}`,
          },
        },
      },
    },
  });
}

onMounted(buildChart);
onBeforeUnmount(() => chart?.destroy());

watch(() => [props.labels, props.datasets], () => {
  if (!chart) return;
  chart.data.labels   = props.labels;
  chart.data.datasets = props.datasets as never;
  chart.update();
}, { deep: true });
</script>

<template>
  <div class="bar-chart-wrap" :style="{ height: (height ?? 200) + 'px' }">
    <canvas ref="canvas" />
  </div>
</template>

<style scoped>
.bar-chart-wrap { width: 100%; }
</style>
