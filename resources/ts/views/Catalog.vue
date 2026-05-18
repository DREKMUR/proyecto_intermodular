<script setup lang="ts">
import {onMounted, ref} from 'vue';
import { useCars } from '@/composables/useCars';
import Pagination from '@/components/Pagination.vue';
import HeaderBase from "@/components/HeaderBase.vue";
import {CarStates, CarTypes} from "@/enums.ts";
import Select from '@volt/Select.vue';
import InputText from '@volt/InputText.vue';
import DatePicker from "@volt/DatePicker.vue";
import Skeleton from "@volt/Skeleton.vue";
import Paginator from "@volt/Paginator.vue";
import type {Brand} from "@/types.ts";
import axios from "axios";
import SecondaryButton from "@volt/SecondaryButton.vue";

const { cars, isLoading, filters, meta, fetchCars, setPage, resetFilters } = useCars();

const stateOptions = [
    { label: 'Disponibles', value: CarStates.Available },
    { label: 'Vendidos', value: CarStates.Sold },
    { label: 'Reservados', value: CarStates.Reserved },
];

const categoryOptions = [
    { label: 'Deportivos', value: CarTypes.Sport },
    { label: 'Deportivos clásicos', value: CarTypes.ClassicSport },
    { label: 'Coupes', value: CarTypes.Coupe },
    { label: 'Clásicos', value: CarTypes.Classic },
    { label: 'Lowriders', value: CarTypes.Lowrider },
    { label: 'Compactos', value: CarTypes.Compact },
    { label: 'SUVs', value: CarTypes.Suv },
]

const brands = ref<Brand[]>([]);

const fetchBrands = () => {
    try{
        axios.get<Brand[]>('/api/brands')
            .then(response => {
                brands.value = response.data;
            });
    } catch (error){
        console.log('Error fetch brands: ', error)
    }
}

const formatPrice = (value: number): string => {
    const n = Number(value);
    if (!Number.isFinite(n)) return '—';
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 2
    }).format(n);
};

onMounted(() => {
    fetchCars();
    fetchBrands();
});
</script>


<template>
    <HeaderBase />
        <div class="w-full mt-10 px-4 gap-10 flex flex-col ">
            <h1 class="text-center text-5xl font-extrabold">
                Catálogo
            </h1>

            <!-- Filtros -->
            <form @submit.prevent class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" aria-label="Filtros de catálogo">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Nombre</label>
                    <InputText
                        class="w-full"
                        v-model="filters.name"
                        placeholder="Buscar por nombre"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Marca</label>
                    <Select
                        class="w-full"
                        v-model="filters.brand"
                        :options="brands"
                        optionLabel="name"
                        optionValue="id"
                        showClear
                        placeholder="Cualquiera"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Estado</label>
                    <Select
                        class="w-full"
                        v-model="filters.state"
                        :options="stateOptions"
                        optionLabel="label"
                        optionValue="value"
                        showClear
                        placeholder="Cualquiera"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Categoría</label>
                    <Select
                        class="w-full"
                        v-model="filters.category"
                        :options="categoryOptions"
                        optionLabel="label"
                        optionValue="value"
                        showClear
                        placeholder="Cualquiera"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Año</label>
                    <DatePicker
                        class="w-full"
                        v-model="filters.year"
                        view="year"
                        dateFormat="yy"
                        showClear
                        :manualInput="false"
                        placeholder="Seleccionar año de producción"
                    />
                </div>

                <div class="flex items-end">
                    <button
                        class="bg-red-600 rounded p-2 w-full hover:bg-red-700 transition-all hover:cursor-pointer font-semibold"
                        @click="resetFilters">
                        Vaciar filtros
                    </button>
                </div>
            </form>

            <!-- Carga -->
            <Skeleton v-if="isLoading" width="100%" height="150px"/>
            <div v-if="!isLoading && cars.length === 0" class="text-white text-xl text-center">No se encontraron resultados :(</div>

            <!-- Productos -->
            <ul v-if="!isLoading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" role="list">
                <li v-for="car in cars" :key="car.id" class="bg-slate-800 rounded-lg shadow-lg overflow-hidden hover:scale-[1.01] transition-transform">
                    <router-link :to="`/cars/${car.id}`" class="block focus:outline-none focus:ring-4 focus:ring-indigo-500">
                        <div class="relative w-full h-44 bg-slate-700">
                            <img
                                v-if="car.imageRoute"
                                :src="car.imageRoute"
                                :alt="car.slug ?? car.name"
                                class="object-cover w-full h-full"
                                loading="lazy"
                            />

                            <span
                                class="absolute top-2 left-2 bg-indigo-600 bg- text-xs px-2 py-1 rounded text-white/95"
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
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold text-white truncate">{{ car.name }}</h3>
                                    <p class="text-xs text-slate-300 truncate">
                                        {{ car.brand }}
                                        <span class="italic text-white"
                                              :class="{
                                                'bg-yellow-500 rounded px-1': car.state === CarStates.Reserved,
                                                'bg-red-600 rounded px-1': car.state === CarStates.Sold,
                                                'bg-green-600 rounded px-1': car.state === CarStates.Available
                                              }"
                                        >
                                            • {{ car.state }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-slate-300">
                                        {{ car.year }}
                                    </div>
                                    <div class="mt-2 text-lg font-bold text-indigo-300">
                                        {{ formatPrice(car.price) }}
                                    </div>
                                </div>
                            </div>

                            <p v-if="car.description" class="mt-3 text-xs text-slate-400 line-clamp-3">{{ car.description }}</p>
                        </div>
                    </router-link>
                </li>
            </ul>

            <!-- Paginación -->
            <div v-if="!isLoading && cars.length > 0" class="my-8 flex items-center justify-center">
                <Paginator
                    :totalRecords="meta.last_page"
                    :rows="8"
                />

            </div>
        </div>
</template>
