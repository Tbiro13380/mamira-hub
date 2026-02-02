<script setup lang="ts">
import type { ToastViewportProps } from "reka-ui"
import { ToastViewport as RekaToastViewport } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<ToastViewportProps>()
</script>

<template>
  <RekaToastViewport
    data-slot="toast-viewport"
    :class="cn(
      'fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]',
      props.class
    )"
    v-bind="props"
  />
</template>

