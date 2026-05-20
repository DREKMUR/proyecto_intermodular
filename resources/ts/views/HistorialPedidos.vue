<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import Skeleton from '@volt/Skeleton.vue';

interface OrderItem {
    id: number;
    product_id: number;
    product_name: string;
    quantity: number;
    price: number;
    product: {
        id: number;
        imageRoute: string;
        slug: string;
        brand: { id: number; name: string } | null;
    };
}

interface Order {
    id: number;
    total: number;
    subtotal: number;
    discount_percent: number;
    shipping_cost: number;
    status: string;
    created_at: string;
    items: OrderItem[];
}

const orders = ref<Order[]>([]);
const loading = ref(true);

const statusLabels: Record<string, string> = {
    pending: 'Pendiente',
    completed: 'Completado',
    shipped: 'Enviado',
    cancelled: 'Cancelado',
};

onMounted(async () => {
    try {
        const res = await axios.get('/api/my-orders');
        orders.value = res.data;
    } catch {
        // fallback: empty
    } finally {
        loading.value = false;
    }
});

function totalItems(items: OrderItem[]): number {
    return items.reduce((sum, it) => sum + it.quantity, 0);
}

function imgUrl(path: string | undefined): string {
    if (!path) return '/images/placeholder.webp';
    if (path.startsWith('http')) return path;
    if (path.startsWith('/')) return path;
    return '/storage/' + path;
}
</script>

<template>
    <HeaderBase />
    <main class="flex-1 flex justify-center px-4 py-12">
        <div class="w-full max-w-5xl">
            <h1 class="text-3xl font-bold mb-8 text-center">Mis Pedidos</h1>

            <div v-if="loading" class="space-y-4">
                <Skeleton v-for="n in 3" :key="n" class="w-full h-24 rounded-xl" />
            </div>

            <div v-else-if="orders.length === 0" class="text-center py-16 text-white/60">
                <i class="pi pi-inbox text-6xl block mb-4"></i>
                <p class="text-xl">No tienes pedidos todavía.</p>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="bg-white/5 rounded-xl p-5 flex flex-col md:flex-row md:items-center gap-4"
                >
                    <div class="flex-1 space-y-1">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-lg">Pedido #{{ order.id }}</span>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full font-semibold"
                                :class="{
                                    'bg-green-600/20 text-green-400': order.status === 'completed',
                                    'bg-yellow-600/20 text-yellow-400': order.status === 'pending',
                                    'bg-blue-600/20 text-blue-400': order.status === 'shipped',
                                    'bg-red-600/20 text-red-400': order.status === 'cancelled',
                                }"
                            >
                                {{ statusLabels[order.status] || order.status }}
                            </span>
                        </div>
                        <p class="text-sm text-white/60">
                            {{ new Date(order.created_at).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                            — {{ totalItems(order.items) }} artículo(s)
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div v-for="item in order.items.slice(0, 4)" :key="item.id" class="w-10 h-10 shrink-0">
                            <img
                                :src="imgUrl(item.product?.imageRoute)"
                                :alt="item.product_name"
                                class="w-full h-full object-cover rounded"
                            />
                        </div>
                        <span v-if="order.items.length > 4" class="text-xs text-white/50">+{{ order.items.length - 4 }}</span>
                    </div>

                    <div class="text-right shrink-0">
                        <p class="text-xl font-bold">{{ order.total.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' }) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <FooterBase />
</template>
