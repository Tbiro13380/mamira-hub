<script setup lang="ts">
import type { ToastTitleProps } from "reka-ui"
import { ToastTitle as RekaToastTitle } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<ToastTitleProps>()
</script>

<template>
  <RekaToastTitle
    data-slot="toast-title"
    :class="cn('text-sm font-semibold', props.class)"
    v-bind="props"
  >
    <slot />
  </RekaToastTitle>
</template>

