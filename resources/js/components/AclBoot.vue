<script setup lang="ts">
import { ability } from '@/acl/ability';
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';

watchEffect(() => {
    const page = usePage();
    const perms = (page.props as any).auth?.permissions ?? [];

    // perms is like: ["users.view", "roles.view", "users.create"]
    const rules = perms.map((p: string) => ({ action: p, subject: 'all' }));

    // Optional: super admin shortcut (if you have it)
    // rules.push({ action: 'manage', subject: 'all' })

    ability.update(rules);
});
</script>

<template></template>
