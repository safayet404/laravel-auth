<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs = [
    {
        title: 'Role Edit',
        href: '/edit',
    },
];

const props = defineProps({
    role: Object,
    rolePermissions: Array,
    permissions: Array,
});

console.log('errr', props.rolePermissions);

const form = useForm({
    name: props.role.name,
    permissions: props.rolePermissions || [],
});
</script>

<template>
    <Head title="Role Edit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3">
            <Link
                href="/roles"
                class="bg-blue cursor-pointer px-3 py-2 text-xs font-medium text-white dark:bg-white dark:text-black"
            >
                Back
            </Link>

            <form class="mx-auto mt-4 max-w-md space-y-6">
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
                        v-for="permission in props.rolePermissions"
                        class="flex items-center space-x-2"
                    >
                        <input
                            type="checkbox"
                            :checked="true"
                            disabled
                            class="form-checkbox h-5 w-5 rounded accent-blue-600 opacity-100"
                        />

                        <span
                            class="text-gray-800 capitalize dark:text-white"
                            >{{ permission }}</span
                        >
                    </label>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style lang="css">
.checked-disabled:disabled {
    opacity: 1; /* remove gray fade */
    cursor: default;
}

.checked-disabled:disabled:checked {
    accent-color: #2563eb; /* Tailwind blue-600 */
}
</style>
