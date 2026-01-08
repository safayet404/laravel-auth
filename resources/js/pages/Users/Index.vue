<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

defineProps({
    users: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3">
            <Link
                href="/users/create"
                class="bg-blue cursor-pointer px-3 py-2 text-xs font-medium text-white dark:bg-white dark:text-black"
            >
                Create
            </Link>

            <table
                class="w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400"
            >
                <thead
                    class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-white"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Title</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in users"
                        class="odd:bg-white even:bg-gray-50 odd:dark:bg-gray-900 even:dark:bg-gray-800"
                    >
                        <td
                            class="text-gray px-6 py-2 font-medium dark:text-white"
                        >
                            {{ user.id }}
                        </td>
                        <td
                            class="text-gray px-6 py-2 font-medium dark:text-white"
                        >
                            {{ user.name }}
                        </td>
                        <td
                            class="text-gray px-6 py-2 font-medium dark:text-white"
                        >
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-2">
                            <button
                                class="bg-blue cursor-pointer px-3 py-2 text-xs font-medium text-white dark:bg-white dark:text-black"
                            >
                                Edit
                            </button>
                            <button
                                class="bg-blue cursor-pointer px-3 py-2 text-xs font-medium text-white dark:bg-red-700"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
