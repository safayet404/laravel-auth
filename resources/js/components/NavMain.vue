<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useActiveUrl } from '@/composables/useActiveUrl';
import { type NavItem } from '@/types';
import { useAbility } from '@casl/vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    items: NavItem[];
}>();

const ability = useAbility();

const visibleItems = computed(() => {
    const filterItems = (items: NavItem[]): NavItem[] =>
        items
            .map((i) => ({
                ...i,
                children: i.children ? filterItems(i.children) : undefined,
            }))
            .filter((i) => {
                const ok =
                    !i.ability ||
                    ability.can(i.ability.action, i.ability.subject);
                const hasChildren = i.children ? i.children.length > 0 : false;
                return ok && (!i.children || hasChildren);
            });

    return filterItems(props.items);
});

const { urlIsActive } = useActiveUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Menus</SidebarGroupLabel>

        <SidebarMenu>
            <SidebarMenuItem v-for="item in visibleItems" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
