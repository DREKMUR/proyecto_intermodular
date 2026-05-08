<script setup lang="ts">
import {onMounted, ref} from 'vue';
import { useCars } from '@/composables/useCars';
import Pagination from '@/components/Pagination.vue';
import HeaderBase from "@/components/HeaderBase.vue";
import { CarStates } from "@/enums.ts";
import Select from '@volt/Select.vue';
import InputText from '@volt/InputText.vue';
import DatePicker from "@volt/DatePicker.vue";
import type {Brand} from "@/types.ts";
import axios from "axios";

const { cars, loading, error, filters, meta, fetchCars, setPage } = useCars();

const stateOptions = [
    { label: 'Disponibles', value: CarStates.Available },
    { label: 'Vendidos', value: CarStates.Sold },
    { label: 'Reservados', value: CarStates.Reserved },
];

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
        <div class="w-full mt-5 px-4 gap-10 flex flex-col">
            <h1 class="text-center text-5xl font-extrabold">
                Catálogo
            </h1>

            <!-- Filtros -->
            <form @submit.prevent class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" aria-label="Filtros de catálogo">
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
                        placeholder="Todas"
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
                        placeholder="Todos"
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
                    />
                </div>
            </form>

            <!-- Grid -->
            <div v-if="loading" class="text-center py-12 text-slate-300">Cargando...</div>
            <div v-if="error" class="text-red-400 mb-4">{{ error }}</div>
            <div v-if="!loading && cars.length === 0" class="text-slate-400">No se encontraron resultados.</div>

            <ul v-if="!loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" role="list">
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
                            <div v-else class="flex items-center justify-center h-full text-slate-400">Sin imagen</div>

                            <span class="absolute top-2 left-2 bg-indigo-600 text-xs px-2 py-1 rounded text-white/95">
                                {{ car.category?.name ?? 'Sin categoría' }}
                            </span>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold text-white truncate">{{ car.name }}</h3>
                                    <p class="text-xs text-slate-300 truncate">{{ car.brand }} • <span class="italic">{{ car.state }}</span></p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-slate-300">{{ car.year }}</div>
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
            <div class="mt-8 flex items-center justify-center">
                <Pagination
                    :current-page="meta.current_page"
                    :last-page="meta.last_page"
                    @change-page="setPage"
                />
            </div>
        </div>
</template>
