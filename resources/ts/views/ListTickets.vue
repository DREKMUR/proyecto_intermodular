<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from "@/stores/auth.ts";
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";

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
}

const authStore = useAuthStore();
const tickets = ref<Ticket[]>([]);
const loading = ref(true);

const fetchTickets = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/ticket');
        tickets.value = data;
    } catch { /* empty state */ }
    finally { loading.value = false; }
};

const statusLabel = (s: number) => s === 1 ? 'Abierto' : 'Cerrado';
const statusClass = (s: number) => s === 1
    ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
    : 'bg-white/10 text-white/50 border-white/10';

onMounted(fetchTickets);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-slate-100">
        <HeaderBase />
        <main class="flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div class="flex items-center gap-4 mb-8">
                <i class="pi pi-ticket text-3xl"></i>
                <h1 class="text-3xl font-black uppercase tracking-wide">Mis tickets</h1>
            </div>

            <div v-if="loading" class="flex justify-center py-16">
                <i class="pi pi-spin pi-spinner text-3xl opacity-30"></i>
            </div>

            <div v-else-if="tickets.length === 0" class="flex flex-col items-center justify-center py-16 bg-black/30 rounded-2xl border border-white/5 text-center px-4">
                <i class="pi pi-ticket text-5xl mb-4 opacity-20"></i>
                <p class="text-lg mb-2">No tienes ningún ticket</p>
                <p class="text-sm opacity-50 mb-6">Los tickets que crees aparecerán aquí.</p>
                <router-link :to="{ name: 'contactUs' }" class="bg-white text-black px-6 py-3 rounded-full font-bold hover:bg-primary hover:text-white transition-colors text-sm">
                    Crear un ticket
                </router-link>
            </div>

            <div v-else class="space-y-4">
                <div v-for="t in tickets" :key="t.id"
                    class="bg-black/30 border border-white/5 rounded-xl p-5 hover:border-white/20 transition-all"
                >
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div>
                            <h3 class="font-bold text-lg">{{ t.product_ref }}</h3>
                            <p class="text-xs opacity-40 mt-0.5">
                                <i class="pi pi-calendar mr-1"></i>{{ new Date(t.created_at).toLocaleDateString() }}
                                <i class="pi pi-clock ml-3 mr-1"></i>{{ new Date(t.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                            </p>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full border" :class="statusClass(t.status)">
                            {{ statusLabel(t.status) }}
                        </span>
                    </div>
                    <p class="text-sm opacity-70 leading-relaxed mb-2">{{ t.description }}</p>
                    <p class="text-xs opacity-40">Ticket #{{ t.id }}</p>
                </div>
            </div>
        </main>
        <FooterBase />
    </div>
</template>
