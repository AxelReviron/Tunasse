<script setup lang="ts">
import { Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement
} from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps<{
    accounts: Array<{ name: string; balance: number }>;
}>();

// TODO: Turn it into a reusable component

const cssVar = (name: string) =>
    getComputedStyle(document.documentElement).getPropertyValue(name).trim();

const chartData = computed(() => {
    return {
        labels: props.accounts.map(a => a.name),
        datasets: [
            {
                data: props.accounts.map(a => a.balance),
                backgroundColor: [
                    '#4ade80', // vert
                    '#f87171', // rouge
                    '#60a5fa', // bleu
                    '#fbbf24', // jaune
                    '#a78bfa'  // violet
                ],
                borderWidth: 1,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: {
            left: 20,   // espace à gauche pour la légende
            right: 20,
            top: 20,
            bottom: 20,
        },
    },
    plugins: {
        legend: {
            display: true,
            position: 'left',
            align: 'center',
            labels: {
                boxWidth: 20,
                padding: 15,
            },
        },
        title: {
            display: true,
            text: 'Total accounts',
            font: {
                size: 18,
            },
            padding: {
                bottom: 0,
            },
        },
        tooltip: {
            callbacks: {
                label: function(context: any) {
                    const value = context.parsed;
                    const label = context.label;
                    return `${label}: €${value.toFixed(2)}`;
                },
            },
        },
    },
};

</script>

<template>
    <Doughnut
        :data="chartData"
        :options="chartOptions"
    />
</template>
