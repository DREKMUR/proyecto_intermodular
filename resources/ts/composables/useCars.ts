import { ref, watch } from 'vue';
import axios from 'axios';
import { debounce } from '@/utils/debounce';
import type { Car, PaginatedResponse } from '@/types.ts';

export function useCars() {
    const cars = ref<Car[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const filters = ref({
        name: '',
        brand: '',
        state: '',
        year: new Date(),
        page: 1,
        per_page: 12,
    });

    const meta = ref({
        current_page: 1,
        last_page: 1,
        total: 0,
        per_page: 12,
    });

    const fetchCars = async () => {
        loading.value = true;
        error.value = null;
        try {
            const params = {
                name: filters.value.name || undefined,
                brand_id: filters.value.brand || undefined,
                state: filters.value.state || undefined,
                year: filters.value.year ? filters.value.year.getFullYear() : undefined,
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
            error.value = e?.response?.data?.message ?? e.message ?? 'Error al cargar coches';
        } finally {
            loading.value = false;
        }
    };

    const debouncedFetch = debounce(() => {
        filters.value.page = 1;
        fetchCars();
    }, 350);

    watch(
        () => [filters.value.name, filters.value.brand, filters.value.state, filters.value.year],
        () => {
            debouncedFetch();
        }
    );

    const setPage = (page: number) => {
        filters.value.page = page;
        fetchCars();
    };

    return {
        cars,
        loading,
        error,
        filters,
        meta,
        fetchCars,
        setPage,
    };
}
