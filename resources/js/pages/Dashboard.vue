<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import {
    CreditCard,
    MessageCircle,
    TrendingUp,
    User,
    Users,
    Wallet,
} from 'lucide-vue-next';
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js';
import { Pie } from 'vue-chartjs';

const props = defineProps<{
    stats: {
        userCount: number;
        customerCount: number;
        feedbackCount: number;
        totalTransactionSum: number | string;
        failedTransactionCount: number;
        successfulTransactionCount: number;
    };
    transactions: Array<{
        id: number | string;
        amount: string | number;
        transaction_status: string;
        customer: { name: string };
    }>;
    users: Array<unknown>;
    customers: Array<{
        id: number | string;
        name: string;
        email: string;
        phone_number: string;
    }>;
}>();

declare global {
    interface Window {
        Echo: { channel: (name: string) => { listen: (event: string, cb: (e: unknown) => void) => void } };
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

ChartJS.register(ArcElement, Tooltip, Legend);

const chartData = computed(() => ({
    labels: ['Failed', 'Successful'],
    datasets: [
        {
            backgroundColor: ['hsl(var(--destructive))', 'hsl(142 76% 36%)'],
            borderWidth: 0,
            data: [
                props.stats?.failedTransactionCount ?? 0,
                props.stats?.successfulTransactionCount ?? 0,
            ],
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' as const },
    },
};

function statusVariant(status: string): 'success' | 'warning' | 'destructive' | 'secondary' {
    switch (status) {
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'failed':
            return 'destructive';
        default:
            return 'secondary';
    }
}

onMounted(() => {
    if (window.Echo) {
        window.Echo.channel('game').listen('GameEvent', (event: unknown) => {
            console.log('Game event received:', event);
        });
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 p-4 md:p-6 lg:p-8">
            <!-- Page header -->
            <header class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-foreground md:text-3xl">
                    Dashboard
                </h1>
                <p class="text-sm text-muted-foreground">
                    Overview of your platform metrics and recent activity.
                </p>
            </header>

            <Separator />

            <!-- Stats grid -->
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card class="overflow-hidden transition-colors hover:bg-muted/30">
                    <CardContent class="flex flex-row items-center gap-4 p-6">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary ring-1 ring-primary/20"
                        >
                            <User class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-muted-foreground">Users</p>
                            <p class="text-2xl font-semibold tabular-nums text-foreground">
                                {{ stats?.userCount ?? 0 }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="overflow-hidden transition-colors hover:bg-muted/30">
                    <CardContent class="flex flex-row items-center gap-4 p-6">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20 dark:text-emerald-400"
                        >
                            <Users class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-muted-foreground">Customers</p>
                            <p class="text-2xl font-semibold tabular-nums text-foreground">
                                {{ stats?.customerCount ?? 0 }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="overflow-hidden transition-colors hover:bg-muted/30">
                    <CardContent class="flex flex-row items-center gap-4 p-6">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20 dark:text-amber-400"
                        >
                            <MessageCircle class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-muted-foreground">Feedback</p>
                            <p class="text-2xl font-semibold tabular-nums text-foreground">
                                {{ stats?.feedbackCount ?? 0 }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="overflow-hidden transition-colors hover:bg-muted/30">
                    <CardContent class="flex flex-row items-center gap-4 p-6">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 ring-1 ring-violet-500/20 dark:text-violet-400"
                        >
                            <Wallet class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-muted-foreground">Total transactions</p>
                            <p class="text-2xl font-semibold tabular-nums text-foreground">
                                {{ stats?.totalTransactionSum ?? 0 }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="overflow-hidden transition-colors hover:bg-muted/30">
                    <CardContent class="flex flex-row items-center gap-4 p-6">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-red-500/10 text-red-600 ring-1 ring-red-500/20 dark:text-red-400"
                        >
                            <CreditCard class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-muted-foreground">Failed</p>
                            <p class="text-2xl font-semibold tabular-nums text-foreground">
                                {{ stats?.failedTransactionCount ?? 0 }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="overflow-hidden transition-colors hover:bg-muted/30">
                    <CardContent class="flex flex-row items-center gap-4 p-6">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20 dark:text-emerald-400"
                        >
                            <TrendingUp class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-muted-foreground">Successful</p>
                            <p class="text-2xl font-semibold tabular-nums text-foreground">
                                {{ stats?.successfulTransactionCount ?? 0 }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <!-- Tables + Chart row -->
            <section class="grid gap-6 lg:grid-cols-2">
                <!-- Recent transactions -->
                <Card class="overflow-hidden">
                    <CardHeader class="pb-3">
                        <CardTitle class="text-base">Recent transactions</CardTitle>
                        <CardDescription>Latest transaction activity</CardDescription>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto rounded-b-lg">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-border bg-muted/50">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                                        >
                                            Customer
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                                        >
                                            Amount
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr
                                        v-for="transaction in transactions"
                                        :key="transaction.id"
                                        class="transition-colors hover:bg-muted/30"
                                    >
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <Avatar class="size-9 shrink-0">
                                                    <AvatarFallback class="text-xs">
                                                        {{
                                                            transaction.customer?.name?.charAt(0)?.toUpperCase() ?? '?'
                                                        }}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <span class="font-medium text-foreground">
                                                    {{ transaction.customer?.name ?? '—' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 tabular-nums text-muted-foreground">
                                            {{ transaction.amount }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <Badge
                                                :variant="
                                                    statusVariant(
                                                        transaction.transaction_status ?? '',
                                                    )
                                                "
                                            >
                                                {{ transaction.transaction_status ?? '—' }}
                                            </Badge>
                                        </td>
                                    </tr>
                                    <tr v-if="!transactions?.length">
                                        <td colspan="3" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                            No recent transactions.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent customers -->
                <Card class="overflow-hidden">
                    <CardHeader class="pb-3">
                        <CardTitle class="text-base">Recent customers</CardTitle>
                        <CardDescription>Newly registered customers</CardDescription>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto rounded-b-lg">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-border bg-muted/50">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                                        >
                                            Name
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                                        >
                                            Email
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                                        >
                                            Phone
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr
                                        v-for="customer in customers"
                                        :key="customer.id"
                                        class="transition-colors hover:bg-muted/30"
                                    >
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <Avatar class="size-9 shrink-0">
                                                    <AvatarFallback class="text-xs">
                                                        {{ customer.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <span class="font-medium text-foreground">
                                                    {{ customer.name ?? '—' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-muted-foreground">
                                            {{ customer.email ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 tabular-nums text-muted-foreground">
                                            {{ customer.phone_number ?? '—' }}
                                        </td>
                                    </tr>
                                    <tr v-if="!customers?.length">
                                        <td colspan="3" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                            No recent customers.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <!-- Transaction statistics chart -->
            <section>
                <Card class="overflow-hidden">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-base">Transaction statistics</CardTitle>
                        <CardDescription>Success vs failed transactions</CardDescription>
                    </CardHeader>
                    <CardContent class="pb-6">
                        <div class="h-[280px]">
                            <Pie :data="chartData" :options="chartOptions" />
                        </div>
                    </CardContent>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
