<script setup lang="ts">
import {
    Settings, ChevronRight, MoreHorizontal, SquarePen,
    Trash, ChevronUp, LogOut, CircleUserRound
} from "lucide-vue-next"

import {
    Sidebar, SidebarContent, SidebarFooter, SidebarGroup,
    SidebarGroupContent, SidebarHeader, SidebarMenu, SidebarMenuAction,
    SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubItem,
    SidebarSeparator,
} from "@/components/ui/sidebar"

import {
    Collapsible, CollapsibleContent, CollapsibleTrigger,
} from "@/components/ui/collapsible"

import {
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";

import { usePage } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue';
import { useIcons } from '@/composables/useIcons';
import { Account, Budget } from '@/types/models';
import { AccountService } from '@/services/api/tunasse/AccountService';
import { BudgetService } from '@/services/api/tunasse/BudgetService';

const page = usePage();

// Services
const accountService = new AccountService();
const budgetService = new BudgetService();

// Composables
const { getIcon } = useIcons();

// Refs
const items = ref<any[]>([
    {
        title: "Dashboard",
        url: "#",
        icon: getIcon('SquareMenu'),
        subItems: []
    },
    {
        title: "Accounts",
        url: "#",
        icon: getIcon('Landmark'),
        subItems: []
    },
    {
        title: "Budgets",
        url: "#",
        icon: getIcon('ChartPie'),
        subItems: []
    },
    {
        title: "Transactions",
        url: "#",
        icon: getIcon('ArrowUpDown'),
        subItems: [
            { title: "All", url: "/transactions/all", icon: getIcon('Banknote') },
            { title: "Expense", url: "/dashboard/overview", icon: getIcon('TrendingUp') },
            { title: "Income", url: "/dashboard/overview", icon: getIcon('TrendingDown') },
        ]
    },
])

const accounts = ref<Account[]>([]);
const budgets = ref<Budget[]>([]);

function fillSideBar() {
    const accountsMenu = accounts.value.map((account: any) => ({
        title: account.name,
        url: `/accounts/${account.id}`,
        icon: getIcon(account.icon)
    }))

    const accountsItem = items.value.find(item => item.title === "Accounts")
    if (accountsItem) {
        accountsItem.subItems = accountsMenu
    }

    const budgetsMenu = budgets.value.map((budget: any) => ({
        title: budget.name,
        url: `/budgets/${budget.id}`,
        icon: getIcon(budget.icon)
    }))

    const budgetsItem = items.value.find(item => item.title === "Budgets")
    if (budgetsItem) {
        budgetsItem.subItems = budgetsMenu
    }
}

onMounted(async () => {
    accounts.value = await accountService.getUserAccounts(page.props.auth.user.id);
    budgets.value = await budgetService.getUserBudgets(page.props.auth.user.id);
    fillSideBar();
})

</script>

<template>
    <Sidebar>
        <SidebarHeader>
            <h1 class="text-lg font-normal ml-2">Tunasse</h1>
        </SidebarHeader>
        <SidebarSeparator class="m-0" />
        <SidebarContent>
            <SidebarGroup>
                <SidebarGroupContent>
                    <SidebarMenu>
                        <Collapsible
                            v-for="item in items"
                            :key="item.title"
                            class="group/collapsible"
                            defaultOpen
                        >
                            <SidebarMenuItem>
                                <CollapsibleTrigger asChild v-if="item.subItems.length > 0">
                                    <SidebarMenuButton class="cursor-pointer">
                                        <component :is="item.icon" />
                                        <span>{{ item.title }}</span>
                                        <ChevronRight v-if="item.subItems.length > 0" class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <a v-else :href="item.url">
                                    <SidebarMenuButton class="cursor-pointer">
                                        <component :is="item.icon" />
                                        <span>{{ item.title }}</span>
                                        <ChevronRight v-if="item.subItems.length > 0" class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                    </SidebarMenuButton>
                                </a>
                                <CollapsibleContent v-if="item.subItems.length > 0">
                                    <SidebarMenuSub>
                                        <SidebarMenuSubItem v-for="subItem in item.subItems" :key="subItem.title">
                                            <SidebarMenuButton asChild size="sm">
                                                <a :href="subItem.url">
                                                    <component :is="subItem.icon" />
                                                    <span>{{ subItem.title }}</span>
                                                </a>
                                            </SidebarMenuButton>

                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <SidebarMenuAction>
                                                        <MoreHorizontal class="h-4 w-4" />
                                                    </SidebarMenuAction>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent side="right" align="start">
                                                    <DropdownMenuItem>
                                                        <SquarePen class="mr-2 h-4 w-4" />
                                                        <span>Edit</span>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem variant="destructive">
                                                        <Trash class="mr-2 h-4 w-4" />
                                                        <span>Delete</span>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>

                                        </SidebarMenuSubItem>
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter class="w-full absolute bottom-0" >
            <SidebarMenu v-if="page.props.auth.user">
                <SidebarMenuItem>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <SidebarMenuButton>
                                <CircleUserRound />
                                {{ page.props.auth.user.name }}
                                <ChevronUp class="ml-auto" />
                            </SidebarMenuButton>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent side="top" class="w-[--reka-popper-anchor-width]">
                            <DropdownMenuItem>
                                <Settings class="h-4 w-4 text-muted-foreground" />
                                <span>Settings</span>
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                                <LogOut class="h-4 w-4 text-muted-foreground" />
                                <span>Sign out</span>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>

    </Sidebar>
</template>
