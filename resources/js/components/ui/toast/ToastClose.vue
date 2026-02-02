<script setup lang="ts">
import type { ToastCloseProps } from "reka-ui"
import { ToastClose as RekaToastClose } from "reka-ui"
import { X } from "lucide-vue-next"
import { cn } from "@/lib/utils"

const props = defineProps<ToastCloseProps>()
</script>

<template>
  <RekaToastClose
    data-slot="toast-close"
    :class="cn(
      'absolute right-2 top-2 rounded-md p-1 text-foreground/50 opacity-0 transition-opacity hover:text-foreground focus:opacity-100 focus:outline-none focus:ring-2 group-hover:opacity-100 group-[.destructive]:text-red-300 group-[.destructive]:hover:text-red-50 group-[.destructive]:focus:ring-red-400 group-[.destructive]:focus:ring-offset-red-600',
      props.class
    )"
    v-bind="props"
  >
    <X class="h-4 w-4" />
  </RekaToastClose>
</template>

