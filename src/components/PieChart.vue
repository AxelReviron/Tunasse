<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js';

Chart.register(PieController, ArcElement, Tooltip, Legend);

const props = defineProps<{
  labels: string[];
  data: number[];
  colors: string[];
  height?: number;
}>();

const canvas = ref<HTMLCanvasElement | null>(null);
let chart: Chart | null = null;

function buildChart() {
  if (!canvas.value) return;
  chart?.destroy();
  chart = new Chart(canvas.value, {
    type: 'pie',
    data: {
      labels: props.labels,
      datasets: [{
        data:            props.data,
        backgroundColor: props.colors.map(c => c + 'CC'),
        borderColor:     props.colors,
        borderWidth: 1.5,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true, position: 'bottom' },
        tooltip: { mode: 'index' },
      },
    },
  });
}

onMounted(buildChart);
onBeforeUnmount(() => chart?.destroy());

watch(() => [props.labels, props.data, props.colors], () => {
  if (!chart) return;
  chart.data.labels = props.labels;
  chart.data.datasets[0].data            = props.data;
  chart.data.datasets[0].backgroundColor = props.colors.map(c => c + 'CC') as never;
  chart.data.datasets[0].borderColor     = props.colors as never;
  chart.update();
}, { deep: true });
</script>

<template>
  <div class="pie-chart-wrap" :style="{ height: (height ?? 260) + 'px' }">
    <canvas ref="canvas" />
  </div>
</template>

<style scoped>
.pie-chart-wrap { width: 100%; }
</style>
