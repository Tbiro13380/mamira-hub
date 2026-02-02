<script setup lang="ts">
import type { ToastProviderProps } from "reka-ui"
import { ToastProvider as RekaToastProvider } from "reka-ui"

const props = withDefaults(defineProps<ToastProviderProps>(), {
  duration: 5000,
})
</script>

<template>
  <RekaToastProvider v-bind="props">
    <slot />
  </RekaToastProvider>
</template>

