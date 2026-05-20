<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import AdminNav from "@/components/admin/AdminNav.vue";
import Skeleton from '@volt/Skeleton.vue';

interface DashboardStats {
    total_cars: number;
    total_orders: number;
    total_users: number;
    low_stock: number;
    out_of_stock: number;
    total_revenue: number;
}

const stats = ref<DashboardStats | null>(null);
const loading = ref(true);

const formatCurrency = (value: number) =>
    new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/admin/dashboard');
        stats.value = data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-slate-100">
        <HeaderBase />
        <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <AdminNav />

            <Skeleton v-if="loading" width="100%" height="200px" />

            <div v-else-if="stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm uppercase tracking-wider opacity-60 font-bold">Productos</h3>
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <i class="pi pi-box text-blue-400"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-black">{{ stats.total_cars }}</p>
                </div>

                <div class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm uppercase tracking-wider opacity-60 font-bold">Pedidos</h3>
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="pi pi-shopping-bag text-green-400"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-black">{{ stats.total_orders }}</p>
                </div>

                <div class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm uppercase tracking-wider opacity-60 font-bold">Usuarios</h3>
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <i class="pi pi-users text-purple-400"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-black">{{ stats.total_users }}</p>
                </div>

                <div class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm uppercase tracking-wider opacity-60 font-bold">Stock Bajo</h3>
                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                            <i class="pi pi-exclamation-triangle text-yellow-400"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-black">{{ stats.low_stock }}</p>
                    <p class="text-xs opacity-50 mt-1">Menos de 5 unidades</p>
                </div>

                <div class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm uppercase tracking-wider opacity-60 font-bold">Sin Stock</h3>
                        <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                            <i class="pi pi-times-circle text-red-400"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-black">{{ stats.out_of_stock }}</p>
                </div>

                <div class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm uppercase tracking-wider opacity-60 font-bold">Ingresos Totales</h3>
                        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center">
                            <i class="pi pi-euro text-emerald-400"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-black">{{ formatCurrency(stats.total_revenue) }}</p>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <router-link :to="{ name: 'admin.products' }"
                    class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-xl bg-blue-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="pi pi-box text-2xl text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Gestionar Productos</h3>
                        <p class="text-sm opacity-60">Añadir, editar stock, descuentos ggenerales</p>
                    </div>
                    <i class="pi pi-arrow-right ml-auto opacity-40 group-hover:opacity-100 transition-opacity"></i>
                </router-link>

                <router-link :to="{ name: 'admin.orders' }"
                    class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-xl bg-green-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="pi pi-shopping-bag text-2xl text-green-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Gestionar Pedidos</h3>
                        <p class="text-sm opacity-60">Ver y actualizar estado de pedidos</p>
                    </div>
                    <i class="pi pi-arrow-right ml-auto opacity-40 group-hover:opacity-100 transition-opacity"></i>
                </router-link>

                <router-link :to="{ name: 'admin.users' }"
                    class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-xl bg-purple-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="pi pi-users text-2xl text-purple-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Gestionar Usuarios</h3>
                        <p class="text-sm opacity-60">Administrar roles y cuentas</p>
                    </div>
                    <i class="pi pi-arrow-right ml-auto opacity-40 group-hover:opacity-100 transition-opacity"></i>
                </router-link>

                <router-link :to="{ name: 'admin.sales' }"
                    class="bg-black/30 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition-all flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-xl bg-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="pi pi-chart-bar text-2xl text-amber-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Gráfico de Ventas</h3>
                        <p class="text-sm opacity-60">Visualizar ventas por producto</p>
                    </div>
                    <i class="pi pi-arrow-right ml-auto opacity-40 group-hover:opacity-100 transition-opacity"></i>
                </router-link>
            </div>
        </main>
        <FooterBase />
    </div>
</template>
