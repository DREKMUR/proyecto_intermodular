import { ref, watch } from 'vue';
import axios from 'axios';
import { debounce } from '@/utils/debounce';
import type { Car, PaginatedResponse } from '@/types.ts';

export function useCars() {
    const cars = ref<Car[]>([]);
    const isLoading = ref(false);

    const filters = ref({
        name: '',
        brand: '',
        state: '',
        category: '',
        year: undefined,
        page: 1,
        per_page: 8,
    });

    const meta = ref({
        current_page: 1,
        last_page: 1,
        total: 0,
        per_page: 8,
    });

    const fetchCars = async () => {
        isLoading.value = true;
        try {
            const params = {
                name: filters.value.name || undefined,
                brand_id: filters.value.brand || undefined,
                state: filters.value.state || undefined,
                category: filters.value.category || undefined,
                year: filters.value.year?.getFullYear() || undefined,
                page: filters.value.page,
                per_page: filters.value.per_page,
            };
            const res = await axios.get<PaginatedResponse<Car>>('/api/cars', { params });
            cars.value = res.data.data;
            meta.value.current_page = res.data.current_page;
            meta.value.last_page = res.data.last_page;
            meta.value.total = res.data.total;
            meta.value.per_page = res.data.per_page;
        } catch (e: any) {
            console.log('Error cargando coches: ', e)
        } finally {
            isLoading.value = false;
        }
    };

    const debouncedFetch = debounce(() => {
        filters.value.page = 1;
        fetchCars();
    }, 350);

    const setPage = (page: number) => {
        filters.value.page = page;
        fetchCars();
    };

    const resetFilters = () => {
        filters.value = {
            name: '',
            brand: '',
            state: '',
            category: '',
            year: undefined,
            page: 1,
            per_page: 12,
        };
    }

    watch(
        () => [filters.value.name, filters.value.brand, filters.value.state, filters.value.year, filters.value.category],
        () => {
            debouncedFetch();
        }
    );

    return {
        cars,
        isLoading,
        filters,
        meta,
        fetchCars,
        setPage,
        resetFilters,
    };
}
