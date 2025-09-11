<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import ChartLine from '@/components/ChartLine.vue';
import { onMounted, ref } from 'vue';
import ChartDoughnut from '@/components/ChartDoughnut.vue';
import { Account, Transaction } from '@/types/models';
import { TransactionService } from '@/services/api/tunasse/TransactionService';
import { AccountService } from '@/services/api/tunasse/AccountService';
import { useTransactions } from '@/composables/useTransactions';

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

// Refs
const monthlyRecurringTransactions = ref<Transaction[]>([]);
const remainingRecurringTransactionsAmount = ref<Transaction[]>([]);
const currentMonthTransactions = ref<Transaction[]>([]);
const accounts = ref<Account[]>([]);


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
                    <ChartDoughnut :accounts="accounts" />
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
                <ChartLine :transactions="currentMonthTransactions"/>
            </div>
        </div>
    </AppLayout>
</template>
