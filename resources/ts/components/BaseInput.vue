<script setup lang="ts">
defineProps<{
    modelValue: string;
    label: string;
    icon?: string;
    type?: string;
    placeholder?: string;
    required?: boolean;
    minlength?: string;
    validation?: 'default' | 'valid' | 'invalid';
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
                :class="[
                    'w-full pl-10 pr-4 py-2.5 rounded outline-none transition-all duration-200',
                    validation === 'valid' ? 'border-green-500 bg-green-50 ring-2 ring-green-500/30' :
                    validation === 'invalid' ? 'border-red-500 bg-red-50 ring-2 ring-red-500/30' :
                    'border-slate-300 bg-white hover:bg-slate-100 focus:bg-slate-100 focus:ring-2 focus:ring-primary focus:border-primary',
                    'text-slate-700 placeholder:text-slate-400'
                ]"
            />
        </div>
    </div>
</template>
