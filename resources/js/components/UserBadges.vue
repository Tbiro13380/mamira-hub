<script setup lang="ts">
import { Trophy } from 'lucide-vue-next';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type Props = {
    badge?: UserBadge | null;
};

const props = defineProps<Props>();
</script>

<template>
    <div
        v-if="badge"
        :title="badge.name"
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium transition-all hover:scale-105"
        :style="{
            backgroundColor: badge.color + '20',
            color: badge.color,
            border: `1px solid ${badge.color}40`,
        }"
    >
        <span v-if="badge.icon" class="text-sm">{{ badge.icon }}</span>
        <Trophy v-else class="h-3 w-3" />
        <span class="hidden sm:inline">{{ badge.name }}</span>
    </div>
</template>

