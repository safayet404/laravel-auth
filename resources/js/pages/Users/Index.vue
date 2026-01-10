<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { can } from '@/lib/can';
import { Head, Link, router } from '@inertiajs/vue3';

const breadcrumbs = [
    {
        title: 'Users',
        href: '/users',
    },
];

const props = defineProps({
    users: Array,
});

console.log(props.users);

function deleteUser(id) {
    if (confirm('Are you sure want to delete this user?')) {
        router.delete(`/users/${id}`);
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3">
            <Link
                v-if="can('users.create')"
                href="/users/create"
                class="mt-5 mb-3 inline-block cursor-pointer rounded-md bg-blue-700 px-3 py-2 text-xs font-medium text-white dark:bg-purple-600 dark:text-white"
            >
                Create User
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
                        <th scope="col" class="px-6 py-3">Roles</th>
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

                        <td
                            class="text-gray px-6 py-2 font-medium dark:text-white"
                        >
                            <span
                                class="mr-1 bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800"
                                v-for="p in user.roles"
                            >
                                {{ p.name }}
                            </span>
                        </td>

                        <td class="px-6 py-2">
                            <Link
                                :href="`/users/${user.id}`"
                                class="bg-blue mr-2 cursor-pointer rounded-2xl px-3 py-2 text-xs font-medium text-white dark:bg-blue-700 dark:text-white"
                            >
                                Show
                            </Link>
                            <Link
                                v-if="can('users.edit')"
                                :href="`/users/${user.id}/edit`"
                                class="bg-blue mr-2 cursor-pointer rounded-2xl px-3 py-2 text-xs font-medium text-white dark:bg-white dark:text-black"
                            >
                                Edit
                            </Link>
                            <button
                                v-if="can('users.delete')"
                                @click="deleteUser(user.id)"
                                class="bg-blue mr-2 cursor-pointer rounded-2xl px-3 py-2 text-xs font-medium text-white dark:bg-red-700"
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
