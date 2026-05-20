<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

import { useToast } from 'primevue/usetoast';
import { useAuthStore } from "@/stores/auth.ts";
import {useCartStore} from "@/stores/cart.ts";

import type {Car, ProductOpinions} from "@/types.ts";
import {CarStates, CarTypes} from "@/enums.ts";

import Skeleton from '@volt/Skeleton.vue';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";

const props = defineProps<{
    slug: string;
    id: number;
}>();

const cartStore = useCartStore();
const authStore = useAuthStore();

const toast = useToast();

const car = ref<Car | null>(null);
const loading = ref<boolean>(true);

const productOpinions = ref<ProductOpinions | null>(null);
const loadingOpinions = ref(false);

const reviewForm = ref({ rating: 0, title: '', opinion: '' });
const submittingReview = ref(false);
const hoverRating = ref(0);

const orderItemId = ref<number | null>(null);

onMounted(async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/cars/${props.id}`);
        car.value = response.data;
    } catch (err) {
        console.error("Error al obtener el coche:", err);
    } finally {
        loading.value = false;
    }

    await fetchOpinions();
});

const fetchOpinions = async () => {
    loadingOpinions.value = true;
    try {
        const { data } = await axios.get(`/api/getOpinions/${props.id}`);
        productOpinions.value = data;
    } catch { /* empty state handled by template */ }
    finally { loadingOpinions.value = false; }
};

const averageRating = computed(() => {
    if (!productOpinions.value || productOpinions.value.totalOpinions === 0) return 0;
    const r = productOpinions.value.ratings;
    const weighted = r.reduce((sum, count, i) => sum + count * (i + 1), 0);
    const total = r.reduce((sum, count) => sum + count, 0);
    return total === 0 ? 0 : weighted / total;
});

const submitReview = async () => {
    if (reviewForm.value.rating === 0 || !reviewForm.value.title.trim() || !reviewForm.value.opinion.trim()) return;
    submittingReview.value = true;
    try {
        await axios.post('/api/sendOpinion', {
            idProduct: String(props.id),
            idUser: authStore.user?.id ? String(authStore.user.id) : null,
            user: authStore.user?.name || 'Anònim',
            rating: reviewForm.value.rating,
            title: reviewForm.value.title,
            text: reviewForm.value.opinion,
            order_item_id: orderItemId.value,
        });
        toast.add({ severity: 'success', summary: 'Opinión enviada', life: 3000 });
        reviewForm.value = { rating: 0, title: '', opinion: '' };
        orderItemId.value = null;
        await fetchOpinions();
    } catch {
        toast.add({ severity: 'error', summary: 'Error al enviar la opinión', life: 3000 });
    } finally { submittingReview.value = false; }
};

const finalPrice = computed(() => {
    if (!car.value) return 0;
    if (car.value.discount > 0) {
        return car.value.price * (1 - car.value.discount / 100);
    }
    return car.value.price;
});

const formatPrice = (value: number) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0
    }).format(value);
};

const handleAddToCart = () => {
    if (!car.value) return;

    if(!car.value.stock){
        toast.add({severity: 'warn', summary: 'No hay suficiente stock.', life: 3000});
        return;
    }

    const result = cartStore.addToCart({
        id: car.value.id,
        name: car.value.name,
        imageRoute: car.value.imageRoute,
        price: finalPrice.value,
        stock: car.value.stock
    });

    if(result) toast.add({severity: 'warn', summary: 'No hay suficiente stock.', life: 3000});

    else toast.add({severity: 'success', summary: 'Producto añadido correctamente.', life: 3000});
};
</script>

<template>
    <div class="flex flex-col min-h-screen">
        <HeaderBase />

        <div v-if="loading" class="flex-1 flex justify-center items-center p-6">
            <Skeleton width="100%" height="400px" class="max-w-xl rounded-xl" />
        </div>

        <div v-else-if="car" class="flex-1 mx-6 sm:mx-16 lg:mx-40 my-8 md:my-16">
            <div class="flex flex-col md:flex-row gap-8 lg:gap-16 items-center md:items-start">
                <div class="w-full md:w-1/2 flex justify-center">
                    <img
                        :src="car.imageRoute"
                        :alt="car.name"
                        class="w-full max-w-xl h-auto object-cover rounded-xl shadow-2xl"
                    />
                </div>

                <div class="w-full md:w-1/2 flex flex-col">
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <span
                            class="bg-indigo-600 text-xs px-2 py-1 rounded text-white/95 font-medium"
                            :class="{
                                        'bg-rose-600': car.category === CarTypes.Sport,
                                        'bg-green-600': car.category === CarTypes.Suv,
                                        'bg-yellow-600': car.category === CarTypes.Compact,
                                        'bg-cyan-600': car.category === CarTypes.ClassicSport,
                                        'bg-orange-600': car.category === CarTypes.Classic,
                                        'bg-purple-600': car.category === CarTypes.Lowrider,
                                        'bg-amber-600': car.category === CarTypes.Coupe,
                                    }"
                        >
                            {{ car.category ?? 'Sin categoría' }}
                        </span>

                        <span class="text-sm font-semibold text-slate-400">
                            Stock: <span :class="car.stock > 0 ? 'text-emerald-400' : 'text-rose-500'">{{ car.stock }} u.</span>
                        </span>
                    </div>

                    <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-slate-100">
                        {{ car.name }} {{ car.year }}
                    </h2>
                    <p class="text-sm text-slate-400 italic mb-6">
                        {{ car.model }}
                    </p>

                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center gap-0.5">
                            <i v-for="n in 5" :key="n"
                                class="text-lg"
                                :class="n <= Math.round(averageRating) ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-white/20'"
                            ></i>
                        </div>
                        <span class="text-sm opacity-60">
                            {{ averageRating > 0 ? averageRating.toFixed(1) : '—' }}
                            <span v-if="productOpinions">({{ productOpinions.totalOpinions }})</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 my-4 bg-slate-950/40 p-4 rounded-xl border border-slate-800/60 text-center">
                        <div>
                            <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Motor</p>
                            <p class="text-xs md:text-sm font-semibold text-slate-200 mt-0.5">{{ car.specs.engine }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Potencia</p>
                            <p class="text-xs md:text-sm font-semibold text-slate-200 mt-0.5">{{ car.specs.hp }} CV</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Combustible</p>
                            <p class="text-xs md:text-sm font-semibold text-slate-200 mt-0.5">{{ car.specs.fuel }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 mt-4 border-t border-slate-800">
                        <div class="flex flex-col">
                            <div
                                v-if="car.discount > 0"
                                class="w-max bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wider shadow-md animate-pulse mb-1"
                            >
                                -{{ car.discount }}%
                            </div>
                            <span v-if="car.discount > 0" class="text-sm text-slate-500 line-through font-medium">
                                {{ formatPrice(car.price) }}
                            </span>
                            <span class="text-3xl font-black text-emerald-400 tracking-tight">
                                {{ formatPrice(finalPrice) }}
                            </span>
                        </div>

                        <button
                            v-if="car.state === CarStates.Available"
                            @click="handleAddToCart"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-primary font-semibold px-6 py-3 rounded-xl hover:bg-primary-hover active:bg-primary-active hover:cursor-pointer transition-all shadow-md"
                        >
                            <i class="pi pi-shopping-cart"></i>
                            Añadir a la cesta
                        </button>

                        <button
                            v-else-if="car.state === CarStates.Reserved"
                            class="w-full sm:w-auto bg-yellow-600 font-semibold px-6 py-3 rounded-xl cursor-not-allowed opacity-80"
                            disabled
                        >
                            Coche reservado
                        </button>

                        <button
                            v-else
                            class="w-full sm:w-auto bg-red-600 font-semibold px-6 py-3 rounded-xl cursor-not-allowed opacity-80"
                            disabled
                        >
                            Coche no disponible
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-16 border-t border-white/10 pt-10">
                <h3 class="text-2xl font-bold mb-8 flex items-center gap-2">
                    <i class="pi pi-star-fill text-yellow-400"></i>
                    Opiniones de los clientes
                    <span v-if="productOpinions" class="text-sm font-normal opacity-50">({{ productOpinions.totalOpinions }})</span>
                </h3>

                <div v-if="loadingOpinions" class="flex justify-center py-8">
                    <i class="pi pi-spin pi-spinner text-3xl opacity-30"></i>
                </div>

                <div v-else-if="productOpinions && productOpinions.opinions.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="op in productOpinions.opinions" :key="op.opinionId"
                        class="bg-white/5 border border-white/10 rounded-xl p-5 hover:border-white/20 transition-colors"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary/30 flex items-center justify-center text-xs font-bold uppercase">
                                    {{ (op.user || 'A')[0] }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">{{ op.user || 'Anònim' }}</p>
                                    <p class="text-[10px] opacity-40">{{ new Date(op.timeStamp).toLocaleDateString() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5">
                                <i v-for="n in 5" :key="n"
                                    class="text-sm"
                                    :class="n <= op.rating ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-white/20'"
                                ></i>
                            </div>
                        </div>
                        <h4 class="font-bold text-base mb-1">{{ op.title }}</h4>
                        <p class="text-sm opacity-70 leading-relaxed">{{ op.opinion }}</p>
                    </div>
                </div>

                <div v-else class="text-center py-12 opacity-50">
                    <i class="pi pi-comment text-4xl mb-3 block"></i>
                    <p>No hay opiniones para este producto.</p>
                    <p class="text-sm mt-1">Sé el primero en opinar!</p>
                </div>

                <div v-if="authStore.isLoggedIn" class="mt-10 bg-white/5 border border-white/10 rounded-xl p-6">
                    <h4 class="text-lg font-bold mb-4">Escribe tu opinión</h4>
                    <div class="flex items-center gap-1 mb-4">
                        <span class="text-sm opacity-60 mr-2">Puntuación:</span>
                        <button v-for="n in 5" :key="n" type="button"
                            @click="reviewForm.rating = n"
                            @mouseenter="hoverRating = n"
                            @mouseleave="hoverRating = 0"
                            class="text-2xl transition-colors cursor-pointer bg-transparent border-0 p-0"
                        >
                            <i class="pi"
                                :class="n <= (hoverRating || reviewForm.rating) ? 'pi-star-fill text-yellow-400' : 'pi-star text-white/20'"
                            ></i>
                        </button>
                    </div>
                    <input v-model="reviewForm.title" type="text" placeholder="Título de tu opinión"
                        class="w-full bg-black/40 border border-white/10 rounded-lg p-3 text-sm focus:outline-none focus:border-primary mb-3"
                    />
                    <textarea v-model="reviewForm.opinion" rows="3" placeholder="Cuenta tu experiencia..."
                        class="w-full bg-black/40 border border-white/10 rounded-lg p-3 text-sm focus:outline-none focus:border-primary resize-none mb-4"
                    ></textarea>
                    <div class="flex justify-end">
                        <button @click="submitReview" :disabled="submittingReview || reviewForm.rating === 0 || !reviewForm.title.trim() || !reviewForm.opinion.trim()"
                            class="flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-primary-hover transition-colors disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer border-0"
                        >
                            <i v-if="submittingReview" class="pi pi-spin pi-spinner"></i>
                            <i v-else class="pi pi-send"></i>
                            {{ submittingReview ? 'Enviando...' : 'Enviar opinión' }}
                        </button>
                    </div>
                </div>

                <div v-else class="mt-10 text-center py-8 border-t border-white/5">
                    <p class="opacity-50"><router-link :to="{ name: 'login' }" class="text-primary hover:underline">Inicia sesión</router-link> para dejar tu opinión.</p>
                </div>
            </div>
        </div>

        <FooterBase />
    </div>
</template>
