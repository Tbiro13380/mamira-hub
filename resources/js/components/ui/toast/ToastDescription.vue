<script setup lang="ts">
import type { ToastDescriptionProps } from "reka-ui"
import { ToastDescription as RekaToastDescription } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<ToastDescriptionProps>()
</script>

<template>
  <RekaToastDescription
    data-slot="toast-description"
    :class="cn('text-sm opacity-90', props.class)"
    v-bind="props"
  >
    <slot />
  </RekaToastDescription>
</template>

