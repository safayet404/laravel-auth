<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs = [
    {
        title: 'Role Create',
        href: '/role',
    },
];

const form = useForm({
    name: '',
    permissions: [],
});

defineProps({
    permissions: Array,
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
                @submit.prevent="form.post('/roles')"
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
                        for="permissions"
                        class="select-one text-sm leading-none font-medium"
                    >
                    </label>
                    <label
                        v-for="permission in permissions"
                        class="flex items-center space-x-2"
                    >
                        <input
                            v-model="form.permissions"
                            :value="permission"
                            type="checkbox"
                            class="form-checkbox h-5 w-5 rounded text-blue-600 focus:ring-2 focus:ring-blue-600"
                        />
                        <span
                            class="text-gray-800 capitalize dark:text-white"
                            >{{ permission }}</span
                        >
                    </label>
                    <p
                        v-if="form.errors.permissions"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.permissions }}
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
