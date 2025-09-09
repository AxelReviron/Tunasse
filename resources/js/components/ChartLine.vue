<script setup lang="ts">
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps<{
    transactions: Array<{ date: string; amount: number }>;
}>();

// TODO: Turn it into a reusable component

const cssVar = (name: string) =>
    getComputedStyle(document.documentElement).getPropertyValue(name).trim();

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    const transaction = props.transactions[context.dataIndex]; // get transaction object
                    const name = transaction.name;
                    const amount = transaction.amount;
                    return `${name} : €${amount.toFixed(2)}`;
                },
            },
        },
        legend: {
            display: true,
        },
    },
    scales: {
        x: {
            title: {
                display: true,
                text: 'Date',
            },
        },
        y: {
            title: {
                display: true,
                text: 'Amount',// TODO: Add currency
            },
            beginAtZero: true,
        },
    },
};

const chartData = computed(() => {
    let cumulative = 0;
    const labels: string[] = [];
    const data: number[] = [];

    props.transactions.forEach((t) => {
        const value = t.type === 'expense' ? -t.amount : t.amount;
        cumulative += value;
        labels.push(t.date);
        data.push(cumulative);
    });

    return {
        labels,
        datasets: [
            {
                label: 'Transactions (current month)',
                data,
                borderColor: cssVar('--foreground'),
                backgroundColor: cssVar('--muted-foreground'),
                fill: false,
                tension: 0.3,
            },
        ],
    };
});

</script>

<template>
    <Line
        id="transactions-line-chart"
        :options="chartOptions"
        :data="chartData"
    />
</template>
