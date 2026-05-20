<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth.ts';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const visible = ref(false);
const pendingItems = ref<{ order_item_id: number; product_id: number; product_name: string }[]>([]);

const checkPending = async () => {
    if (!authStore.isLoggedIn) return;
    try {
        const { data } = await axios.get('/api/check-pending-opinion');
        if (data.has_pending) {
            pendingItems.value = data.items;
            visible.value = true;
        }
    } catch { /* ignore */ }
};

const dismiss = async (orderItemId: number) => {
    try {
        await axios.post('/api/dismiss-pending-opinion', { order_item_id: orderItemId });
        pendingItems.value = pendingItems.value.filter(i => i.order_item_id !== orderItemId);
        if (pendingItems.value.length === 0) visible.value = false;
    } catch { /* ignore */ }
};

const goToProduct = async (productId: number) => {
    visible.value = false;
    try {
        const { data } = await axios.get(`/api/cars/${productId}`);
        router.push({ name: 'car.show', params: { slug: data.slug }, state: { id: productId } });
    } catch {
        router.push({ name: 'catalog' });
    }
};

defineExpose({ checkPending });
</script>

<template>
    <Teleport to="body">
        <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm text-slate-100">
            <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                        <i class="pi pi-star-fill text-yellow-400"></i>
                    </div>
                    <div>
                    <h3 class="text-lg font-bold">Valora tus productos!</h3>
                    <p class="text-sm opacity-60">Comparte tu experiencia</p>
                    </div>
                </div>

                <p class="text-sm opacity-80 mb-4">
                    Tienes productos pendientes de valorar. Tu opinión ayuda a mejorar!
                </p>

                <div class="space-y-2 mb-6">
                    <div v-for="item in pendingItems" :key="item.order_item_id"
                        class="flex items-center justify-between bg-white/5 rounded-lg p-3"
                    >
                        <span class="text-sm font-semibold">{{ item.product_name }}</span>
                        <div class="flex gap-2">
                            <button @click="goToProduct(item.product_id)"
                                class="text-xs bg-primary text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-primary-hover transition-colors cursor-pointer border-0"
                            >Opinar</button>
                            <button @click="dismiss(item.order_item_id)"
                                class="text-xs bg-transparent border border-white/20 text-white/60 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-colors cursor-pointer"
                            >Ahora no</button>
                        </div>
                    </div>
                </div>

                <button @click="visible = false"
                    class="w-full text-center text-sm opacity-50 hover:opacity-100 transition-colors py-2 cursor-pointer bg-transparent border-0"
                >Recuérdamelo más tarde</button>
            </div>
        </div>
    </Teleport>
</template>
