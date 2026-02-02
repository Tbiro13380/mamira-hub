<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { CheckCircle2, XCircle } from 'lucide-vue-next';
import { Toast, ToastClose, ToastDescription, ToastProvider, ToastTitle, ToastViewport } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';

const { toasts, removeToast } = useToast();

const variantClasses = computed(() => ({
    success: 'border-green-500 bg-green-50 dark:bg-green-950 dark:border-green-800',
    destructive: 'border-red-500 bg-red-50 dark:bg-red-950 dark:border-red-800',
    default: 'border-border bg-background',
}));

const variantIcons = {
    success: CheckCircle2,
    destructive: XCircle,
    default: null,
};

const openStates = ref<Record<string, boolean>>({});

watch(
    toasts,
    (newToasts) => {
        newToasts.forEach((toast) => {
            if (!(toast.id in openStates.value)) {
                openStates.value[toast.id] = true;
            }
        });
    },
    { deep: true }
);

const handleOpenChange = (toastId: string, open: boolean) => {
    openStates.value[toastId] = open;
    if (!open) {
        removeToast(toastId);
        delete openStates.value[toastId];
    }
};
</script>

<template>
  <ToastProvider>
    <ToastViewport />
    <Toast
      v-for="toast in toasts"
      :key="toast.id"
      :class="variantClasses[toast.variant]"
      :open="openStates[toast.id] ?? true"
      @update:open="(open) => handleOpenChange(toast.id, open)"
    >
      <div class="grid gap-1">
        <div class="flex items-center gap-2">
          <component
            v-if="variantIcons[toast.variant]"
            :is="variantIcons[toast.variant]"
            class="h-4 w-4"
            :class="{
              'text-green-600 dark:text-green-400': toast.variant === 'success',
              'text-red-600 dark:text-red-400': toast.variant === 'destructive',
            }"
          />
          <ToastTitle>{{ toast.title }}</ToastTitle>
        </div>
        <ToastDescription v-if="toast.description">
          {{ toast.description }}
        </ToastDescription>
      </div>
      <ToastClose />
    </Toast>
  </ToastProvider>
</template>


