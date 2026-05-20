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

    if(!car.value.stock){
        toast.add({severity: 'warn', summary: 'No hay suficiente stock.', life: 3000});
        return;
    }

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
    <div class="flex flex-col min-h-screen">
        <HeaderBase />

        <div v-if="loading" class="flex-1 flex justify-center items-center p-6">
            <Skeleton width="100%" height="400px" class="max-w-xl rounded-xl" />
        </div>

        <div v-else-if="car" class="flex-1 mx-6 sm:mx-16 lg:mx-40 my-8 md:my-16 flex flex-col md:flex-row gap-8 lg:gap-16 items-center md:items-start">
            <div class="w-full md:w-1/2 flex justify-center">
                <img
                    :src="car.imageRoute"
                    :alt="car.name"
                    class="w-full max-w-xl h-auto object-cover rounded-xl shadow-2xl"
                />
            </div>

            <div class="w-full md:w-1/2 flex flex-col">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <span
                        class="bg-indigo-600 text-xs px-2 py-1 rounded text-white/95 font-medium"
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

                    <span class="text-sm font-semibold text-slate-400">
                        Stock: <span :class="car.stock > 0 ? 'text-emerald-400' : 'text-rose-500'">{{ car.stock }} u.</span>
                    </span>
                </div>

                <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-slate-100">
                    {{ car.name }} {{ car.year }}
                </h2>
                <p class="text-sm text-slate-400 italic mb-6">
                    {{ car.model }}
                </p>

                <div class="grid grid-cols-3 gap-2 my-4 bg-slate-950/40 p-4 rounded-xl border border-slate-800/60 text-center">
                    <div>
                        <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Motor</p>
                        <p class="text-xs md:text-sm font-semibold text-slate-200 mt-0.5">{{ car.specs.engine }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Potencia</p>
                        <p class="text-xs md:text-sm font-semibold text-slate-200 mt-0.5">{{ car.specs.hp }} CV</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Combustible</p>
                        <p class="text-xs md:text-sm font-semibold text-slate-200 mt-0.5">{{ car.specs.fuel }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 mt-4 border-t border-slate-800">
                    <div class="flex flex-col">
                        <div
                            v-if="car.discount > 0"
                            class="w-max bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wider shadow-md animate-pulse mb-1"
                        >
                            -{{ car.discount }}%
                        </div>
                        <span v-if="car.discount > 0" class="text-sm text-slate-500 line-through font-medium">
                            {{ formatPrice(car.price) }}
                        </span>
                        <span class="text-3xl font-black text-emerald-400 tracking-tight">
                            {{ formatPrice(finalPrice) }}
                        </span>
                    </div>

                    <button
                        v-if="car.state === CarStates.Available"
                        @click="handleAddToCart"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 bg-primary font-semibold px-6 py-3 rounded-xl hover:bg-primary-hover active:bg-primary-active hover:cursor-pointer transition-all shadow-md"
                    >
                        <i class="pi pi-shopping-cart"></i>
                        Añadir a la cesta
                    </button>

                    <button
                        v-else-if="car.state === CarStates.Reserved"
                        class="w-full sm:w-auto bg-yellow-600 font-semibold px-6 py-3 rounded-xl cursor-not-allowed opacity-80"
                        disabled
                    >
                        Coche reservado
                    </button>

                    <button
                        v-else
                        class="w-full sm:w-auto bg-red-600 font-semibold px-6 py-3 rounded-xl cursor-not-allowed opacity-80"
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
