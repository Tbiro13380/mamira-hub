<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
    src?: string;
    alt?: string;
};

const props = withDefaults(defineProps<Props>(), {
    src: '/images/logo.png',
    alt: 'Logo',
});
</script>

<template>
    <img
        :src="src"
        :alt="alt"
        :class="className"
        v-bind="$attrs"
        class="h-full w-full object-contain"
    />
</template>
