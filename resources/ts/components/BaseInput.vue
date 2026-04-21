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
        <label class="text-sm font-semibold text-slate-700">{{ label }}</label>
        <div class="relative">
            <i :class="[icon, 'absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400']"></i>
            <input
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value); $emit('input')"
                @blur="$emit('blur')"
                :type="type || 'text'"
                :required="required"
                :placeholder="placeholder"
                :minlength="minlength"
                class="w-full pl-10 pr-4 py-2.5 focus:bg-slate-200 hover:bg-slate-200 border border-slate-300 rounded text-slate-700/70 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
            />
        </div>
    </div>
</template>
