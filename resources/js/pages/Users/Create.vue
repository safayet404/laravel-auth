<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Create',
        href: '/create',
    },
];

const form = useForm({
    name: '',
});
</script>

<template>
    <Head title="User Create" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3">
            <Link
                href="/users/index"
                class="bg-blue cursor-pointer px-3 py-2 text-xs font-medium text-white dark:bg-white dark:text-black"
            >
                Back
            </Link>
        </div>
    </AppLayout>
</template>
