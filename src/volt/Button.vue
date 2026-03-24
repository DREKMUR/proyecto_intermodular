<template>
    <Button
        unstyled
        :pt="theme"
        :ptOptions="{
            mergeProps: ptViewMerge
        }"
    >
        <template v-for="(_, slotName) in $slots" #[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Button>
</template>

<script setup lang="ts">
import Button, { type ButtonPassThroughOptions, type ButtonProps } from 'primevue/button';
import { ref } from 'vue';
import { ptViewMerge } from './utils';

interface Props extends /* @vue-ignore */ ButtonProps {}
defineProps<Props>();

const theme = ref<ButtonPassThroughOptions>({
    root: `inline-flex cursor-pointer select-none items-center justify-center overflow-hidden relative
        px-3 py-2 gap-2 rounded-sm disabled:pointer-events-none disabled:opacity-60 transition-colors duration-200
        bg-secondary enabled:hover:bg-secondary-hover enabled:active:bg-secondary-active text-tertiary-soft
        border border-secondary enabled:hover:border-secondary-hover enabled:active:border-secondary-active
        focus-visible:outline focus-visible:outline-1 focus-visible:outline-offset-2
        p-vertical:flex-col p-fluid:w-full p-fluid:p-icon-only:w-10
        p-icon-only:w-10 p-icon-only:px-0 p-icon-only:gap-0
        p-icon-only:p-rounded:rounded-full p-icon-only:p-rounded:h-10
        p-small:text-sm p-small:px-[0.625rem] p-small:py-[0.375rem]
        p-large:text-[1.125rem] p-large:px-[0.875rem] p-large:py-[0.625rem]
        p-raised:shadow-xl p-rounded:rounded-[2rem]
        p-outlined:bg-transparent enabled:hover:p-outlined:bg-secondary enabled:active:p-outlined:bg-secondary
        p-outlined:border-secondary enabled:hover:p-outlined:border-secondary enabled:active:p-outlined:border-secondary
        p-outlined:text-secondary enabled:hover:p-outlined:text-secondary enabled:active:p-outlined:text-secondary
        p-text:bg-transparent enabled:hover:p-text:bg-secondary enabled:active:p-text:bg-secondary
        p-text:border-transparent enabled:hover:p-text:border-transparent enabled:active:p-text:border-transparent
        p-text:text-secondary enabled:hover:p-text:text-secondary enabled:active:p-text:text-secondary
    `,
    loadingIcon: `animate-spin`,
    icon: `p-right:order-1 p-bottom:order-2`,
    label: `font-medium p-icon-only:invisible p-icon-only:w-0
        p-small:text-sm p-large:text-[1.125rem]`,
    pcBadge: {
        root: `min-w-4 h-4 leading-4 bg-primary-contrast rounded-full text-primary text-xs font-bold`
    }
});
</script>
