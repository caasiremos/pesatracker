<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { UserGroupIcon, MagnifyingGlassIcon, PencilIcon } from '@heroicons/vue/24/outline';



const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

watch(search, (value) => {
    router.get(
        route('customers.index'),
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customers',
        href: '/customers',
    },
];
</script>

<template>

    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="py-1">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="mb-4 flex justify-between items-center">
                                <div class="relative flex-grow mr-4">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                    </div>
                                    <input v-model="search" type="text" name="search" id="search"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Search customers by name" />
                                </div>
                            </div>
                            <div v-if="customers?.data?.length === 0" class="text-center py-12">
                                <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No customers found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or add a new customer.
                                </p>
                            </div>
                            <div v-else>
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Phone Number</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="customer in customers?.data" :key="customer.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                                            {{ customer.name.charAt(0).toUpperCase() }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ customer.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ customer.email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ customer.phone_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                    :class="{
                                                        'bg-green-100 text-green-800': customer.status === 'active',
                                                        'bg-red-100 text-red-800': customer.status === 'inactive',
                                                        'bg-yellow-100 text-yellow-800': customer.status === 'pending',
                                                        'bg-gray-100 text-gray-800': !['active', 'inactive', 'pending'].includes(customer.status)
                                                    }">
                                                    {{ customer.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                <div class="mt-4">
                                    <div v-if="customers?.links?.length > 3" class="flex flex-wrap -mb-1">
                                        <template v-for="(link, key) in customers?.links" :key="key">
                                            <div v-if="link.url === null"
                                                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                                                v-html="link.label" />
                                            <Link v-else
                                                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                                                :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url"
                                                v-html="link.label" />
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>