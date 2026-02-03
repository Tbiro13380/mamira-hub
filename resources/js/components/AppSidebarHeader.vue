<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { Bell } from 'lucide-vue-next';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const auth = computed(() => page.props.auth as { user?: any } | null);

const unreadCount = computed(() => {
    const count = (page.props as any).unreadNotificationsCount;
    return typeof count === 'number' ? count : 0;
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2 flex-1">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        
        <Button
            v-if="auth?.user"
            variant="ghost"
            size="icon"
            class="relative h-9 w-9 cursor-pointer hover:bg-accent"
            @click="router.visit('/notificacoes')"
            title="Notificações"
        >
            <Bell class="h-5 w-5" />
            <span
                v-if="unreadCount > 0"
                class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-background z-10"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </Button>
    </header>
</template>
