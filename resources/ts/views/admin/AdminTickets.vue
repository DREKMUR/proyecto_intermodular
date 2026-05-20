<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import AdminNav from "@/components/admin/AdminNav.vue";
import Skeleton from '@volt/Skeleton.vue';
import Select from '@volt/Select.vue';

interface Ticket {
    id: number;
    user_id: number | null;
    name: string;
    email: string;
    product_ref: string;
    description: string;
    status: number;
    created_at: string;
    updated_at: string;
    user: { id: number; name: string; email: string } | null;
}

const tickets = ref<Ticket[]>([]);
const loading = ref(true);
const statusFilter = ref('');

const statusOptions = [
    { label: 'Todos', value: '' },
    { label: 'Abiertos', value: '1' },
    { label: 'Cerrados', value: '2' },
];

const fetchTickets = async () => {
    loading.value = true;
    try {
        const params: Record<string, any> = {};
        if (statusFilter.value) params.status = statusFilter.value;
        const { data } = await axios.get('/api/admin/tickets', { params });
        tickets.value = data.data;
    } catch { /* empty */ }
    finally { loading.value = false; }
};

const closeTicket = async (id: number) => {
    try {
        await axios.put(`/api/admin/tickets/${id}/status`, { status: 2 });
        await fetchTickets();
    } catch { /* ignore */ }
};

const reopenTicket = async (id: number) => {
    try {
        await axios.put(`/api/admin/tickets/${id}/status`, { status: 1 });
        await fetchTickets();
    } catch { /* ignore */ }
};

const statusLabel = (s: number) => s === 1 ? 'Abierto' : 'Cerrado';
const statusClass = (s: number) => s === 1
    ? 'bg-emerald-500/20 text-emerald-400'
    : 'bg-white/10 text-white/50';

onMounted(fetchTickets);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <AdminNav />

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold flex items-center gap-2"><i class="pi pi-ticket"></i> Tickets</h2>
                <Select
                    v-model="statusFilter"
                    :options="statusOptions"
                    optionLabel="label"
                    optionValue="value"
                    @change="fetchTickets"
                    class="w-44 text-sm"
                    placeholder="Filtrar"
                />
            </div>

            <Skeleton v-if="loading" width="100%" height="300px" />

            <div v-else class="overflow-x-auto bg-black/30 rounded-2xl border border-white/5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-left">
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">ID</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Usuario</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Email</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Producto</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Descripción</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Data</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Estat</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in tickets" :key="t.id" class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="p-3 font-mono">#{{ t.id }}</td>
                            <td class="p-3 font-semibold">{{ t.user?.name || t.name }}</td>
                            <td class="p-3 opacity-70">{{ t.user?.email || t.email }}</td>
                            <td class="p-3 font-medium">{{ t.product_ref }}</td>
                            <td class="p-3 max-w-xs truncate opacity-80">{{ t.description }}</td>
                            <td class="p-3 text-xs opacity-60">{{ new Date(t.created_at).toLocaleDateString() }}</td>
                            <td class="p-3">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="statusClass(t.status)">
                                    {{ statusLabel(t.status) }}
                                </span>
                            </td>
                            <td class="p-3">
                                <button v-if="t.status === 1"
                                    @click="closeTicket(t.id)"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border-0 cursor-pointer transition-colors bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30"
                                >Cerrar</button>
                                <button v-else
                                    @click="reopenTicket(t.id)"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border-0 cursor-pointer transition-colors bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30"
                                >Reabrir</button>
                            </td>
                        </tr>
                        <tr v-if="tickets.length === 0">
                            <td colspan="8" class="p-6 text-center opacity-50">No hay tickets.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
        <FooterBase />
    </div>
</template>
