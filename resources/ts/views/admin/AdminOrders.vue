<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import AdminNav from "@/components/admin/AdminNav.vue";
import Skeleton from '@volt/Skeleton.vue';
import Select from '@volt/Select.vue';
import Button from '@volt/Button.vue';

const orders = ref<any[]>([]);
const loading = ref(true);

const formatCurrency = (v: number) =>
    new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(v);

const statusOptions = [
    { label: 'Pendiente', value: 'pending' },
    { label: 'Completado', value: 'completed' },
    { label: 'Enviado', value: 'shipped' },
    { label: 'Cancelado', value: 'cancelled' },
];

const fetchOrders = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/admin/orders');
        orders.value = data.data;
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
};

const updateStatus = async (id: number, status: string) => {
    try {
        await axios.put(`/api/admin/orders/${id}`, { status });
        await fetchOrders();
    } catch (e) { console.error(e); }
};

const deleteOrder = async (id: number) => {
    if (!confirm('¿Eliminar este pedido?')) return;
    try {
        await axios.delete(`/api/admin/orders/${id}`);
        await fetchOrders();
    } catch (e) { console.error(e); }
};

onMounted(fetchOrders);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <AdminNav />

            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2"><i class="pi pi-shopping-bag"></i> Pedidos</h2>

            <Skeleton v-if="loading" width="100%" height="300px" />

            <div v-else class="overflow-x-auto bg-black/30 rounded-2xl border border-white/5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-left">
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">ID</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Cliente</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Email</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Productos</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Total</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Fecha</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Estado</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="o in orders" :key="o.id" class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="p-3 font-mono">#{{ o.id }}</td>
                            <td class="p-3 font-semibold">{{ o.user?.name ?? '—' }}</td>
                            <td class="p-3 opacity-70">{{ o.user?.email ?? '—' }}</td>
                            <td class="p-3">
                                <div v-for="item in o.items" :key="item.id" class="text-xs">
                                    {{ item.product_name }} x{{ item.quantity }} — {{ formatCurrency(item.price * item.quantity) }}
                                </div>
                            </td>
                            <td class="p-3 font-bold font-mono">{{ formatCurrency(o.total) }}</td>
                            <td class="p-3 text-xs opacity-60">{{ new Date(o.created_at).toLocaleDateString() }}</td>
                            <td class="p-3">
                                <Select
                                    :modelValue="o.status"
                                    @update:modelValue="(val: string) => updateStatus(o.id, val)"
                                    :options="statusOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    class="w-36"
                                />
                            </td>
                            <td class="p-3">
                                <Button icon="pi pi-trash" class="p-error bg-transparent border-transparent text-red-400 hover:bg-red-500/20" @click="deleteOrder(o.id)" />
                            </td>
                        </tr>
                        <tr v-if="orders.length === 0">
                            <td colspan="8" class="p-6 text-center opacity-50">No hay pedidos.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
        <FooterBase />
    </div>
</template>
