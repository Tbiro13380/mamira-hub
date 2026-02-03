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
        class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium transition-all hover:scale-105 shrink-0"
        :style="{
            backgroundColor: badge.color + '20',
            color: badge.color,
            border: `1px solid ${badge.color}40`,
        }"
    >
        <span v-if="badge.icon" class="text-xs leading-none">{{ badge.icon }}</span>
        <Trophy v-else class="h-2.5 w-2.5" />
        <span class="hidden sm:inline truncate max-w-[60px]">{{ badge.name }}</span>
    </div>
</template>

