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
    email: '',
    password: '',
});
</script>

<template>
    <Head title="User Create" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3">
            <Link
                href="/users"
                class="bg-blue cursor-pointer px-3 py-2 text-xs font-medium text-white dark:bg-white dark:text-black"
            >
                Back
            </Link>

            <form
                @submit.prevent="form.post('/users')"
                class="mx-auto mt-4 max-w-md space-y-6"
            >
                <div class="grid gap-2">
                    <label
                        for="name"
                        class="select-one text-sm leading-none font-medium"
                        >Name</label
                    >

                    <input
                        id="name"
                        name="name"
                        v-model="form.name"
                        class="border-gra mt-1 block w-full rounded-md border px-3 py-2 text-base"
                        placeholder="Enter your name"
                    />
                    <p
                        v-if="form.errors.name"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>
                <div class="grid gap-2">
                    <label
                        for="email"
                        class="select-one text-sm leading-none font-medium"
                        >Email</label
                    >

                    <input
                        id="email"
                        name="email"
                        v-model="form.email"
                        type="email"
                        class="border-gra mt-1 block w-full rounded-md border px-3 py-2 text-base"
                        placeholder="Enter your email"
                    />
                    <p
                        v-if="form.errors.email"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>
                <div class="grid gap-2">
                    <label
                        for="password"
                        class="select-one text-sm leading-none font-medium"
                        >Passowrd</label
                    >

                    <input
                        id="password"
                        name="password"
                        v-model="form.password"
                        type="password"
                        class="border-gra mt-1 block w-full rounded-md border px-3 py-2 text-base"
                        placeholder="Enter your password"
                    />
                    <p
                        v-if="form.errors.password"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <button
                    type="submit"
                    class="rounded-md bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700"
                >
                    Submit
                </button>
            </form>
        </div>
    </AppLayout>
</template>
