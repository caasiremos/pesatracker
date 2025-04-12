<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { UserIcon, BeakerIcon, ShieldExclamationIcon, AcademicCapIcon, DocumentTextIcon, ClipboardDocumentListIcon, ExclamationTriangleIcon, ClockIcon } from '@heroicons/vue/24/solid';
import { CreditCard, CreditCardIcon, LucideCreditCard, MessageCircle, User, User2, Users2, UsersIcon } from 'lucide-vue-next';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'

declare global {
    interface Window {
        Echo: any; // you can replace `any` with the proper type if you import it
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

ChartJS.register(ArcElement, Tooltip, Legend)

const chartData = {
    labels: ['Failed Transactions', 'Successful Transactions'],
    datasets: [
        {
            backgroundColor: ['#EF4444', '#10B981'],  // red-500 and green-500 to match your existing color scheme
            data: [5000, 70000]  // Using your static figures from the dashboard
        }
    ]
}

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
}

onMounted(() => {
    listen();
});

function listen() {
    window.Echo.channel('game')
        .listen('GameEvent', (event: any) => {
            console.log('Game event received:', event);

        });
}
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Users Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center">
                                <User class="h-8 w-8 text-blue-500 mr-4" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Users</p>
                                    <p class="text-2xl font-semibold text-gray-900">100</p>
                                </div>
                            </div>
                        </div>

                        <!-- Treatments Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center">
                                <UsersIcon class="h-8 w-8 text-green-500 mr-4" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Customers</p>
                                    <p class="text-2xl font-semibold text-gray-900">100</p>
                                </div>
                            </div>
                        </div>

                        <!-- Common Diseases Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center">
                                <MessageCircle class="h-8 w-8 text-yellow-500 mr-4" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Feedback</p>
                                    <p class="text-2xl font-semibold text-gray-900">100</p>
                                </div>
                            </div>
                        </div>

                        <!-- Drugs Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center">
                                 <LucideCreditCard class="h-8 w-8 text-purple-500 mr-4" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transactions</p>
                                    <p class="text-2xl font-semibold text-gray-900">UGX 1,000,0000,000</p>
                                </div>
                            </div>
                        </div>

                        <!-- Students Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center">
                                <CreditCard class="h-8 w-8 text-red-500 mr-4" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Failed Transactions</p>
                                    <p class="text-2xl font-semibold text-gray-900">5,000</p>
                                </div>
                            </div>
                        </div>

                        <!-- Leave Chits Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center">
                                <LucideCreditCard class="h-8 w-8 text-green-500 mr-4" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Successful Transactions</p>
                                    <p class="text-2xl font-semibold text-gray-900">70,000</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New row for Low Stock and Expired Drugs -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Low Stock Drugs Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Customer
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Amount
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                some name
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                some name
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                some name
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Expired Drugs Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Customer Registration</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>

                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Phone Number
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                some again
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                some again
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                some again
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Add this after your grid of cards and before the "Recent Transactions" section -->
                    <div class="mt-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Statistics</h3>
                            <div class="h-[300px]">
                                <Pie :data="chartData" :options="chartOptions" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
