<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import ChartLine from '@/components/ChartLine.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import ChartDoughnut from '@/components/ChartDoughnut.vue';

const page = usePage()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// TODO: Type this
const monthlyRecurringTransactions = ref([]);
const remainingRecurringTransactionsCount = ref();
const remainingRecurringTransactionsAmount = ref();

const currentMonthTransactions = ref([]);

const accounts = ref([]);


function calculateRemainingRecurringTransactions(): void {
    const totalAmount = monthlyRecurringTransactions.value.reduce(
        (sum: number, tx: any) => sum + Number(tx.amount),
        0
    );

    remainingRecurringTransactionsCount.value = monthlyRecurringTransactions.value.length;
    remainingRecurringTransactionsAmount.value = totalAmount;
}

// TODO: Refactor this (make a service)
async function getUserMonthlyRecurringExpenseSinceToday(): void {
    const now = new Date();

    // Date of the current day
    const today = now.toISOString().split('T')[0];

    // Last day of the month
    const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0)
        .toISOString()
        .split('T')[0];

    try {
        const response = await axios.post(`/api/transactions/search`, {
            filters: {
                is_recurring: { eq: true },
                recurring_interval: { eq: 1 },
                recurring_unit: { eq: 'month' },
                date: {
                    gte: today,
                    lte: endOfMonth
                },
                type: {
                    eq: 'expense',
                },
            }
        });

        monthlyRecurringTransactions.value = response.data.data;
    } catch (error) {
        console.error({ error });
    }
}

async function getUserCurrentMonthTransactions(): Promise<void> {
    const now = new Date();

    // Start of month (YYYY-MM-DD)
    const start = new Date(now.getFullYear(), now.getMonth(), 1)
        .toISOString()
        .split("T")[0];

    // End of month (YYYY-MM-DD)
    const today = now.toISOString().split('T')[0];

    try {
        const response = await axios.post(`/api/transactions/search`, {
            filters: {
                date: {
                    between: [start, today]
                }
            },
            sort: 'date',
            direction: 'asc',
        });

        currentMonthTransactions.value = response.data.data;
    } catch (error) {
        console.error("Error while fetching transactions:", error);
    }
}

async function getUserAccounts() {
    try {
        const response = await axios.post(`/api/accounts/search`, {
            filters: {
                user_id: {
                    eq: page.props.auth.user.id
                }
            },
            sort: 'date',
            direction: 'asc',
        });

        accounts.value = response.data.data;
        console.log(accounts.value);
    } catch (error) {
        console.error("Error while fetching transactions:", error);
    }
}

onMounted(async () => {
    await getUserMonthlyRecurringExpenseSinceToday();
    calculateRemainingRecurringTransactions();

    await getUserCurrentMonthTransactions();

    await getUserAccounts()
})

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="flex justify-center items-center relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <ChartDoughnut :accounts="accounts" />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                    <div class="text-gray-500 dark:text-gray-300 mb-2">Remaining Recurring Transactions</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ remainingRecurringTransactionsCount }}</div>
                    <div class="text-gray-500 dark:text-gray-300 mt-4">Amount</div>
                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ remainingRecurringTransactionsAmount }}</div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                    <div>Stocks and Crypto prices (to come)</div>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <ChartLine :transactions="currentMonthTransactions"/>
            </div>
        </div>
    </AppLayout>
</template>
