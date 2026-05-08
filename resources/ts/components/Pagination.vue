<script lang="ts">
import { defineComponent, computed } from 'vue';
import type { PropType } from 'vue';

export default defineComponent({
    name: 'Pagination',
    props: {
        currentPage: { type: Number as PropType<number>, required: true },
        lastPage: { type: Number as PropType<number>, required: true },
        maxButtons: { type: Number as PropType<number>, default: 7 },
    },
    emits: ['change-page'],
    setup(props, { emit }) {
        const pages = computed(() => {
            const total = props.lastPage;
            const current = props.currentPage;
            const max = props.maxButtons;
            const half = Math.floor(max / 2);
            let start = Math.max(1, current - half);
            let end = Math.min(total, current + half);

            if (end - start + 1 < max) {
                if (start === 1) {
                    end = Math.min(total, start + max - 1);
                } else if (end === total) {
                    start = Math.max(1, end - max + 1);
                }
            }

            const arr: number[] = [];
            for (let i = start; i <= end; i++) arr.push(i);
            return arr;
        });

        const change = (page: number) => {
            if (page < 1 || page > props.lastPage) return;
            emit('change-page', page);
        };

        return { pages, change };
    },
});
</script>

<template>
    <nav class="flex items-center justify-center space-x-2" aria-label="Paginación">
        <button :disabled="currentPage <= 1" @click="change(currentPage - 1)" class="px-3 py-1 rounded bg-white border disabled:opacity-50">Anterior</button>

        <button
            v-for="p in pages"
            :key="p"
            @click="change(p)"
            :aria-current="p === currentPage ? 'page' : undefined"
            :class="['px-3 py-1 rounded border', p === currentPage ? 'bg-indigo-600 text-white' : 'bg-white']"
        >
            {{ p }}
        </button>

        <button :disabled="currentPage >= lastPage" @click="change(currentPage + 1)" class="px-3 py-1 rounded bg-white border disabled:opacity-50">Siguiente</button>
    </nav>
</template>
