<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import AdminNav from "@/components/admin/AdminNav.vue";
import InputText from '@volt/InputText.vue';
import InputNumber from '@volt/InputNumber.vue';
import Select from '@volt/Select.vue';
import Skeleton from '@volt/Skeleton.vue';
import type { Brand } from "@/types.ts";
import { CarStates, CarTypes } from "@/enums.ts";

const products = ref<any[]>([]);
const brands = ref<Brand[]>([]);
const loading = ref(true);
const search = ref('');
const sortField = ref('name');
const sortDir = ref('asc');

const showAddModal = ref(false);
const showEditModal = ref(false);
const showBrandModal = ref(false);
const editingProduct = ref<any>(null);
const editingBrand = ref<Brand | null>(null);
const bulkDiscountValue = ref<number>(0);
const showBulkDiscount = ref(false);

const form = ref({
    name: '', brand_id: null as number | null, model: '', year: new Date().getFullYear(),
    price: 0, stock: 0, category: 'Deportivo', imageRoute: '', discount: 0,
});

const brandForm = ref({ name: '', logo: '', country: '' });

const catTypes = Object.values(CarTypes).map(t => ({ label: t, value: t }));
const stateOptions = Object.values(CarStates).map(s => ({ label: s, value: s }));

const formatPrice = (v: number) =>
    new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(v);

const fetchAll = async () => {
    loading.value = true;
    try {
        const [p, b] = await Promise.all([
            axios.get('/api/admin/products', { params: { sort: sortField.value, dir: sortDir.value, search: search.value || undefined } }),
            axios.get('/api/admin/brands'),
        ]);
        products.value = p.data.data;
        brands.value = b.data;
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
};

const sortBy = (field: string) => {
    if (sortField.value === field) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    else { sortField.value = field; sortDir.value = 'asc'; }
    fetchAll();
};

const openAdd = () => {
    form.value = { name: '', brand_id: null, model: '', year: new Date().getFullYear(), price: 0, stock: 0, category: 'Deportivo', imageRoute: '', discount: 0 };
    showAddModal.value = true;
};

const openEdit = (p: any) => {
    editingProduct.value = { ...p };
    showEditModal.value = true;
};

const saveProduct = async () => {
    try {
        await axios.post('/api/admin/products', {
            ...form.value,
            specs: JSON.stringify({ engine: '', hp: 0, fuel: '' })
        });
        showAddModal.value = false;
        await fetchAll();
    } catch (e) { console.error(e); }
};

const updateProduct = async () => {
    try {
        const payload: Record<string, any> = {};
        const fields = ['name', 'stock', 'price', 'discount', 'state', 'imageRoute', 'brand_id', 'model', 'year', 'category'];
        for (const key of fields) {
            if (key in editingProduct.value) payload[key] = (editingProduct.value as any)[key];
        }
        await axios.put(`/api/admin/products/${editingProduct.value.id}`, payload);
        showEditModal.value = false;
        await fetchAll();
    } catch (e) { console.error(e); }
};

const deleteProduct = async (id: number) => {
    if (!confirm('¿Eliminar este producto?')) return;
    try {
        await axios.delete(`/api/admin/products/${id}`);
        await fetchAll();
    } catch (e) { console.error(e); }
};

const applyBulkDiscount = async () => {
    try {
        await axios.post('/api/admin/products/bulk-discount', { discount: bulkDiscountValue.value });
        showBulkDiscount.value = false;
        bulkDiscountValue.value = 0;
        await fetchAll();
    } catch (e) { console.error(e); }
};

const openBrandEdit = (b: Brand | null) => {
    editingBrand.value = b ? { ...b } : null;
    brandForm.value = { name: b?.name || '', logo: b?.logo || '', country: b?.country || '' };
    showBrandModal.value = true;
};

const saveBrand = async () => {
    try {
        if (editingBrand.value) {
            await axios.put(`/api/admin/brands/${editingBrand.value.id}`, brandForm.value);
        } else {
            await axios.post('/api/admin/brands', brandForm.value);
        }
        showBrandModal.value = false;
        const { data } = await axios.get('/api/admin/brands');
        brands.value = data;
    } catch (e) { console.error(e); }
};

const deleteBrand = async (id: number) => {
    if (!confirm('¿Eliminar marca?')) return;
    try {
        await axios.delete(`/api/admin/brands/${id}`);
        const { data } = await axios.get('/api/admin/brands');
        brands.value = data;
    } catch (e) { console.error(e); }
};

const sortIcon = (field: string) => {
    if (sortField.value !== field) return 'pi pi-sort';
    return sortDir.value === 'asc' ? 'pi pi-sort-up-fill' : 'pi pi-sort-down-fill';
};

onMounted(fetchAll);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <AdminNav />

            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <h2 class="text-2xl font-bold flex items-center gap-2"><i class="pi pi-box"></i> Productos</h2>
                <div class="flex flex-wrap gap-3">
                    <button @click="showBulkDiscount = !showBulkDiscount" class="flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 px-4 py-2 rounded-xl font-semibold transition-colors text-sm cursor-pointer">
                        <i class="pi pi-percentage"></i> Descuento General
                    </button>
                    <button @click="openBrandEdit(null)" class="flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 px-4 py-2 rounded-xl font-semibold transition-colors text-sm cursor-pointer">
                        <i class="pi pi-tag"></i> Marcas
                    </button>
                    <button @click="openAdd" class="flex items-center gap-2 bg-primary hover:bg-primary-hover px-4 py-2 rounded-xl font-semibold transition-colors text-sm cursor-pointer">
                        <i class="pi pi-plus"></i> Añadir Producto
                    </button>
                </div>
            </div>

            <div v-if="showBulkDiscount" class="bg-black/30 border border-yellow-500/30 rounded-2xl p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold flex items-center gap-2"><i class="pi pi-percentage text-yellow-400"></i> Descuento General</h3>
                    <button @click="showBulkDiscount = false" class="text-white/50 hover:text-white transition-colors cursor-pointer"><i class="pi pi-times text-lg"></i></button>
                </div>
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs uppercase tracking-wider opacity-60">Porcentaje de descuento</label>
                        <InputNumber v-model="bulkDiscountValue" :min="0" :max="100" class="w-32" />
                    </div>
                    <button @click="applyBulkDiscount" class="flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 px-6 py-2 rounded-xl font-semibold transition-colors cursor-pointer">
                        <i class="pi pi-check"></i> Aplicar a Todos
                    </button>
                </div>
            </div>

            <div class="mb-4 max-w-xs">
                <InputText v-model="search" placeholder="Buscar por nombre..." @input="fetchAll" />
            </div>

            <Skeleton v-if="loading" width="100%" height="300px" />

            <div v-else class="overflow-x-auto bg-black/30 rounded-2xl border border-white/5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-left">
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Img</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs cursor-pointer hover:opacity-100" @click="sortBy('name')">
                                Nombre <i :class="sortIcon('name')"></i>
                            </th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Marca</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs cursor-pointer hover:opacity-100" @click="sortBy('stock')">
                                Stock <i :class="sortIcon('stock')"></i>
                            </th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs cursor-pointer hover:opacity-100" @click="sortBy('sold')">
                                Vendidos <i :class="sortIcon('sold')"></i>
                            </th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs cursor-pointer hover:opacity-100" @click="sortBy('price')">
                                Precio <i :class="sortIcon('price')"></i>
                            </th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Dto</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Estado</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in products" :key="p.id" class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="p-3">
                                <img :src="p.imageRoute" :alt="p.name" class="w-10 h-10 object-cover rounded-lg" />
                            </td>
                            <td class="p-3 font-semibold">{{ p.name }}</td>
                            <td class="p-3 opacity-70">{{ p.brand?.name ?? '—' }}</td>
                            <td class="p-3">
                                <span :class="p.stock <= 0 ? 'text-red-400' : p.stock < 5 ? 'text-yellow-400' : 'text-emerald-400'"
                                      class="font-bold">{{ p.stock }} u.</span>
                            </td>
                            <td class="p-3">{{ p.total_sold ?? 0 }}</td>
                            <td class="p-3 font-mono">{{ formatPrice(p.price) }}</td>
                            <td class="p-3">
                                <span v-if="p.discount > 0" class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-xs font-bold">{{ p.discount }}%</span>
                                <span v-else class="opacity-30">—</span>
                            </td>
                            <td class="p-3">
                                <span :class="{'text-green-400': p.state === 'Disponible','text-yellow-400': p.state === 'Reservado','text-red-400': p.state === 'Vendido'}"
                                      class="text-xs font-bold uppercase">{{ p.state }}</span>
                            </td>
                            <td class="p-3">
                                <div class="flex gap-2">
                                    <button @click="openEdit(p)" class="p-2 hover:bg-white/10 rounded-lg transition-colors cursor-pointer" title="Editar">
                                        <i class="pi pi-pencil"></i>
                                    </button>
                                    <button @click="deleteProduct(p.id)" class="p-2 hover:bg-red-500/20 rounded-lg transition-colors text-red-400 cursor-pointer" title="Eliminar">
                                        <i class="pi pi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="9" class="p-6 text-center opacity-50">No hay productos.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Añadir Producto -->
            <Teleport to="body">
                <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" @click.self="showAddModal = false">
                    <div class="bg-white rounded-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-slate-800"><i class="pi pi-plus-circle mr-2 text-primary"></i> Nuevo Producto</h3>
                            <button @click="showAddModal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i class="pi pi-times text-lg"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1 col-span-2">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Nombre</label>
                                <InputText v-model="form.name" placeholder="Nombre del producto" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Marca</label>
                                <Select v-model="form.brand_id" :options="brands" optionLabel="name" optionValue="id" placeholder="Seleccionar" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Modelo</label>
                                <InputText v-model="form.model" placeholder="Ej. Civic" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Año</label>
                                <InputNumber v-model="form.year" :min="1900" :max="2099" :useGrouping="false" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Categoría</label>
                                <Select v-model="form.category" :options="catTypes" optionLabel="label" optionValue="value" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Precio (€)</label>
                                <InputNumber v-model="form.price" :min="0" :step="0.01" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Stock</label>
                                <InputNumber v-model="form.stock" :min="0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Descuento (%)</label>
                                <InputNumber v-model="form.discount" :min="0" :max="100" />
                            </div>
                            <div class="flex flex-col gap-1 col-span-2">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">URL Imagen</label>
                                <InputText v-model="form.imageRoute" placeholder="/images/coche.avif" />
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button @click="showAddModal = false" class="px-4 py-2 rounded-xl font-semibold bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button @click="saveProduct" class="flex items-center gap-2 px-4 py-2 rounded-xl font-semibold bg-primary text-white hover:bg-primary-hover transition-colors cursor-pointer">
                                <i class="pi pi-check"></i> Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Modal Editar Producto -->
            <Teleport to="body">
                <div v-if="showEditModal && editingProduct" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" @click.self="showEditModal = false">
                    <div class="bg-white rounded-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-slate-800"><i class="pi pi-pencil mr-2 text-primary"></i> Editar Producto</h3>
                            <button @click="showEditModal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i class="pi pi-times text-lg"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1 col-span-2">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Nombre</label>
                                <InputText v-model="editingProduct.name" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Stock</label>
                                <InputNumber v-model="editingProduct.stock" :min="0" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Precio (€)</label>
                                <InputNumber v-model="editingProduct.price" :min="0" :step="0.01" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Descuento (%)</label>
                                <InputNumber v-model="editingProduct.discount" :min="0" :max="100" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Estado</label>
                                <Select v-model="editingProduct.state" :options="stateOptions" optionLabel="label" optionValue="value" />
                            </div>
                            <div class="flex flex-col gap-1 col-span-2">
                                <label class="text-xs uppercase tracking-wider text-slate-500 font-semibold">URL Imagen</label>
                                <InputText v-model="editingProduct.imageRoute" />
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button @click="showEditModal = false" class="px-4 py-2 rounded-xl font-semibold bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button @click="updateProduct" class="flex items-center gap-2 px-4 py-2 rounded-xl font-semibold bg-primary text-white hover:bg-primary-hover transition-colors cursor-pointer">
                                <i class="pi pi-check"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Modal Marcas -->
            <Teleport to="body">
                <div v-if="showBrandModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" @click.self="showBrandModal = false">
                    <div class="bg-white rounded-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-slate-800"><i class="pi pi-tag mr-2 text-cyan-500"></i> Gestionar Marcas</h3>
                            <button @click="showBrandModal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i class="pi pi-times text-lg"></i>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <InputText v-model="brandForm.name" placeholder="Nombre" class="flex-1" />
                            <InputText v-model="brandForm.country" placeholder="País" class="w-28" />
                            <button @click="saveBrand" class="flex items-center gap-2 px-4 py-2 rounded-xl font-semibold bg-primary text-white hover:bg-primary-hover transition-colors cursor-pointer shrink-0">
                                <i class="pi pi-plus"></i>
                            </button>
                        </div>
                        <div v-for="b in brands" :key="b.id" class="flex items-center justify-between py-2 border-b border-slate-100">
                            <div>
                                <span class="font-semibold text-slate-700">{{ b.name }}</span>
                                <span v-if="b.country" class="text-xs text-slate-400 ml-2">{{ b.country }}</span>
                                <span class="text-xs text-slate-400 ml-2">({{ b.cars_count ?? 0 }} coches)</span>
                            </div>
                            <div class="flex gap-2">
                                <button @click="openBrandEdit(b)" class="p-1 hover:bg-slate-100 rounded transition-colors text-slate-400 hover:text-slate-600 cursor-pointer"><i class="pi pi-pencil text-sm"></i></button>
                                <button @click="deleteBrand(b.id)" class="p-1 hover:bg-red-100 rounded transition-colors text-red-400 cursor-pointer"><i class="pi pi-trash text-sm"></i></button>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button @click="showBrandModal = false" class="px-4 py-2 rounded-xl font-semibold bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors cursor-pointer">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>
        </main>
        <FooterBase />
    </div>
</template>
