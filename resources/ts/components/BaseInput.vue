<script setup lang="ts">
defineProps<{
    modelValue: string;
    label: string;
    icon: string;
    type?: string;
    placeholder?: string;
    required?: boolean;
    minlength?: string;
}>();

defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'blur'): void;
    (e: 'input'): void;
}>();
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-[var(--p-surface-800)]">{{ label }}</label>
        <div class="relative">
            <i :class="[icon, 'absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--p-text-muted-color)]']"></i>
            <input
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value); $emit('input')"
                @blur="$emit('blur')"
                :type="type || 'text'"
                :required="required"
                :placeholder="placeholder"
                :minlength="minlength"
                class="w-full pl-10 pr-4 py-2.5 bg-[var(--p-surface-50)] border border-[var(--p-content-border-color)] rounded-[var(--p-content-border-radius)] text-[var(--p-text-color)] placeholder:text-[var(--p-surface-400)] focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
            />
        </div>
    </div>
</template>
