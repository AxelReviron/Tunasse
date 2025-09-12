<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import ChartLine from '@/components/ChartLine.vue';
import { computed, onMounted, ref } from 'vue';
import ChartDoughnut from '@/components/ChartDoughnut.vue';
import { Account, Transaction } from '@/types/models';
import { TransactionService } from '@/services/api/tunasse/TransactionService';
import { AccountService } from '@/services/api/tunasse/AccountService';
import { useTransactions } from '@/composables/useTransactions';
import { useCssVar } from '@/composables/useCssVar';

const page = usePage()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Services
const transactionService = new TransactionService();
const accountService = new AccountService();

// Composables
const { calculateTotalAmountTransactions } = useTransactions();
const { getCssVar } = useCssVar();

// Refs
const monthlyRecurringTransactions = ref<Transaction[]>([]);
const remainingRecurringTransactionsAmount = ref<Transaction[]>([]);
const currentMonthTransactions = ref<Transaction[]>([]);
const accounts = ref<Account[]>([]);

//--region Doughnut Chart--
const doughnutChartData = computed(() => {// TODO: Make composable ?
    return {
        labels: accounts.value.map(a => a.name),
        datasets: [
            {
                data: accounts.value.map(a => a.balance),
                backgroundColor: [
                    '#4ade80',
                    '#f87171',
                    '#60a5fa',
                    '#fbbf24',
                    '#a78bfa'
                ],
                borderWidth: 1,
            },
        ],
    };
});

const doughnutChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: {
            left: 20,
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
//--endregion--

//--region Line Chart--
const lineChartData = computed(() => {
    let cumulative = 0;
    const labels: string[] = [];
    const data: number[] = [];

    currentMonthTransactions.value.forEach((t) => {
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
                borderColor: getCssVar('--foreground'),
                backgroundColor: getCssVar('--muted-foreground'),
                fill: false,
                tension: 0.3,
            },
        ],
    };
});

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    const transaction = currentMonthTransactions.value[context.dataIndex]; // get transaction object
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
//--endregion--

onMounted(async () => {
    monthlyRecurringTransactions.value = await transactionService.getUserMonthlyRecurringExpenseSinceToday(page.props.auth.user.id);
    currentMonthTransactions.value = await transactionService.getUserCurrentMonthTransactions(page.props.auth.user.id);

    accounts.value = await accountService.getUserAccounts(page.props.auth.user.id);

    remainingRecurringTransactionsAmount.value = calculateTotalAmountTransactions(monthlyRecurringTransactions.value);
})

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="flex justify-center items-center relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <ChartDoughnut
                        :chartData="doughnutChartData"
                        :chartOptions="doughnutChartOptions"
                    />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                    <div class="text-gray-500 dark:text-gray-300 mb-2">Remaining Recurring Transactions</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ monthlyRecurringTransactions.length }}</div>
                    <div class="text-gray-500 dark:text-gray-300 mt-4">Amount</div>
                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ remainingRecurringTransactionsAmount }}</div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                    <div>Stocks and Crypto prices (to come)</div>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <ChartLine
                    :chartData="lineChartData"
                    :chartOptions="lineChartOptions"
                />
            </div>
        </div>
    </AppLayout>
</template>
