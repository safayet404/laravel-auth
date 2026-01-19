<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const breadcrumbs = [
    {
        title: 'Message',
        href: '/compliance',
    },
];
</script>

<template>
    <Head title="Message Create" />

    <AppLayout :breadcrumbs="breadcrumbs"> </AppLayout>
</template>
