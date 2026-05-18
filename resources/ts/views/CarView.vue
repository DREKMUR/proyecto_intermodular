<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

import { useToast } from 'primevue/usetoast';
import {useCartStore} from "@/stores/cart.ts";

import type {Car} from "@/types.ts";
import {CarStates, CarTypes} from "@/enums.ts";

import Skeleton from '@volt/Skeleton.vue';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";

const props = defineProps<{
    slug: string;
    id: number;
}>();

const cartStore = useCartStore();

const toast = useToast();

const car = ref<Car | null>(null);
const loading = ref<boolean>(true);

onMounted(async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/cars/${props.id}`);
        car.value = response.data;
    } catch (err) {
        console.error("Error al obtener el coche:", err);
    } finally {
        loading.value = false;
    }
});

const finalPrice = computed(() => {
    if (!car.value) return 0;
    if (car.value.discount > 0) {
        return car.value.price * (1 - car.value.discount / 100);
    }
    return car.value.price;
});

const formatPrice = (value: number) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0
    }).format(value);
};

const handleAddToCart = () => {
    if (!car.value) return;

    const result = cartStore.addToCart({
        id: car.value.id,
        name: car.value.name,
        imageRoute: car.value.imageRoute,
        price: finalPrice.value,
        stock: car.value.stock
    });

    if(result) toast.add({severity: 'warn', summary: 'No hay suficiente stock.', life: 3000});

    else toast.add({severity: 'success', summary: 'Producto añadido correctamente.', life: 3000});
};
</script>

<template>
    <div class="flex flex-col h-screen">
        <HeaderBase />

        <div v-if="loading" class="text-center text-slate-400">
            <Skeleton width="500" height="500" />
        </div>

        <div v-else-if="car" class="h-screen flex">
            <div class="flex justify-center w-1/2 m-4">
                    <img
                        :src="car.imageRoute"
                        :alt="car.name"
                        class="h-4/5 object-cover object-center rounded shadow-2xl"
                    />
            </div>
            <div class="m-5 w-1/2">
                <span
                        class="bg-indigo-600 text-xs px-2 py-1 rounded text-white/95"
                        :class="{
                                    'bg-rose-600': car.category === CarTypes.Sport,
                                    'bg-green-600': car.category === CarTypes.Suv,
                                    'bg-yellow-600': car.category === CarTypes.Compact,
                                    'bg-cyan-600': car.category === CarTypes.ClassicSport,
                                    'bg-orange-600': car.category === CarTypes.Classic,
                                    'bg-purple-600': car.category === CarTypes.Lowrider,
                                    'bg-amber-600': car.category === CarTypes.Coupe,
                                }"
                    >
                        {{ car.category ?? 'Sin categoría' }}
                    </span>

                    <h2 class="text-xl font-extrabold tracking-tight text-slate-100 line-clamp-1">
                        {{ car.name }} {{ car.year }}
                    </h2>
                    <p class="text-xs text-slate-400 italic mb-4">
                        {{ car.model }}
                    </p>

                    <div class="grid grid-cols-3 gap-2 my-4 bg-slate-950/40 p-3 rounded-xl border border-slate-800/60 text-center">
                        <div>
                            <p class="text-[9px] uppercase text-slate-500 font-bold tracking-wider">Motor</p>
                            <p class="text-xs font-semibold text-slate-200 mt-0.5">{{ car.specs.engine }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] uppercase text-slate-500 font-bold tracking-wider">Potencia</p>
                            <p class="text-xs font-semibold text-slate-200 mt-0.5">{{ car.specs.hp }} CV</p>
                        </div>
                        <div>
                            <p class="text-[9px] uppercase text-slate-500 font-bold tracking-wider">Combustible</p>
                            <p class="text-xs font-semibold text-slate-200 mt-0.5">{{ car.specs.fuel }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div class="flex flex-col">
                            <div
                                v-if="car.discount > 0"
                                class="bg-red-500 text-white text-xs font-black px-2.5 py-1 rounded-md uppercase tracking-wider shadow-md animate-pulse mb-2"
                            >
                                -{{ car.discount }}%
                            </div>
                            <span v-if="car.discount > 0" class="text-xs text-slate-500 line-through font-medium">
                                {{ formatPrice(car.price) }}
                            </span>
                            <span class="text-2xl font-black text-emerald-400 tracking-tight">
                                {{ formatPrice(finalPrice) }}
                            </span>
                        </div>

                        <button
                            v-if="car.state === CarStates.Available"
                            @click="handleAddToCart"
                            class="bg-primary font-semibold p-2 rounded hover:bg-primary-hover active:bg-primary-active hover:cursor-pointer transition-all"
                        >
                            <i class="pi pi-shopping-cart"></i>
                            Añadir a la cesta
                        </button>

                        <button
                            v-else-if="car.state === CarStates.Reserved"
                            class="bg-yellow-600 font-semibold p-2 rounded hover:cursor-pointer transition-all"
                            disabled
                        >
                            Coche reservado
                        </button>

                        <button
                            v-else
                            class="bg-red-600 font-semibold p-2 rounded hover:cursor-pointer transition-all"
                            disabled
                        >
                            Coche no disponible
                        </button>
                    </div>
                </div>
            </div>
        <FooterBase />
    </div>
</template>
