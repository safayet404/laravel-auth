<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { can } from '@/lib/can';
import { Head, Link, router } from '@inertiajs/vue3';
// No need to import EasyDataTable here since it's global

const props = defineProps({
    students: Array,
});

const headers = [
    { text: 'ID', value: 'id', sortable: true },
    { text: 'NAME', value: 'first_name', sortable: true },
    { text: 'PERMISSIONS', value: 'permissions' }, // We will use a slot for this
    { text: 'ACTIONS', value: 'operation', width: 250 },
];

const breadcrumbs = [
    {
        title: 'Student',
        href: '/student',
    },
];

function deleteRole(id) {
    if (confirm('Are you sure want to delete this role?')) {
        router.delete(`/roles/${id}`);
    }
}
</script>

<template>
    <Head title="Student List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-xl font-semibold dark:text-white">Students</h1>
                <Link
                    href="/student/create"
                    class="rounded-md bg-blue-700 px-4 py-2 text-sm font-medium text-white dark:bg-purple-600"
                >
                    Create Student
                </Link>
            </div>

            <EasyDataTable
                :headers="headers"
                :items="students"
                :rows-per-page="10"
                buttons-pagination
                table-class-name="customize-table"
                class="rounded-lg shadow-md"
            >
                <template #item-permissions="{ permissions }">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="p in permissions"
                            :key="p.id"
                            class="rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200"
                        >
                            {{ p.name }}
                        </span>
                    </div>
                </template>

                <template #item-operation="item">
                    <div class="flex items-center gap-2 py-2">
                        <Link
                            :href="`/roles/${item.id}`"
                            class="rounded bg-blue-600 px-3 py-1 text-xs text-white hover:bg-blue-700"
                        >
                            Show
                        </Link>

                        <Link
                            v-if="can('roles.edit')"
                            :href="`/roles/${item.id}/edit`"
                            class="rounded bg-gray-200 px-3 py-1 text-xs text-black hover:bg-gray-300 dark:bg-white"
                        >
                            Edit
                        </Link>

                        <button
                            v-if="can('roles.delete')"
                            @click="deleteRole(item.id)"
                            class="rounded bg-red-600 px-3 py-1 text-xs text-white hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </div>
                </template>
            </EasyDataTable>
        </div>
    </AppLayout>
</template>

<style>
/* Optional: Match the table theme to your Tailwind colors */
.customize-table {
    --easy-table-header-background-color: #f9fafb;
    --easy-table-header-font-color: #374151;
    --easy-table-row-border: 1px solid #e5e7eb;
}

.dark .customize-table {
    --easy-table-body-row-background-color: #111827;
    --easy-table-body-row-font-color: #d1d5db;
    --easy-table-header-background-color: #374151;
    --easy-table-header-font-color: #ffffff;
    --easy-table-row-border: 1px solid #374151;
    --easy-table-footer-background-color: #111827;
    --easy-table-footer-font-color: #ffffff;
}
</style>
