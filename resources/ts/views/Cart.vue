<script setup lang="ts">
import { useCartStore } from "@/stores/cart";
import HeaderBase from "@/components/HeaderBase.vue";
import type {CartItem} from "@/types.ts";
import {useToast} from "primevue/usetoast";
import {ref} from "vue";
import {useAuthStore} from "@/stores/auth.ts";
import {useRouter} from "vue-router";
import FooterBase from "@/components/FooterBase.vue";

const cartStore = useCartStore();
const authStore = useAuthStore();
const toast = useToast();
const router = useRouter();

const promoCode = ref<string>('');
const havePromoCode = ref<boolean>(false);

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const goToCheckout = () => {
    if(!authStore.isLoggedIn) {
        toast.add({severity: 'warn', summary: 'Necesitas iniciar sesión para proceder con el pago.', life: 3000});
    }
    else
        router.push('/checkout');
};

const handleAddQty = (item: CartItem) => {
    const result = cartStore.addToCart(item);

    if(result) toast.add({severity: 'warn', summary: 'No hay suficiente stock.', life: 3000});
}

const applyPromo = () => {
    if (promoCode.value.trim() === 'APRUEBAME1') {
        toast.add({severity: 'success', summary: 'Código de descuento añadido con éxito.', life: 3000});
        havePromoCode.value = true;
        cartStore.discountPercent = 20;
    } else {
        toast.add({severity: 'warn', summary: 'Código de descuento no reconocido.', life: 3000});
    }
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />

        <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <h1 class="text-2xl md:text-3xl font-bold mb-6 md:mb-8 italic">TU CESTA</h1>

            <div v-if="cartStore.items.length === 0" class="flex flex-col items-center justify-center py-16 md:py-20 bg-black/20 rounded-2xl border border-white/10 text-center px-4">
                <i class="pi pi-shopping-cart text-5xl md:text-6xl mb-4 opacity-20"></i>
                <p class="text-lg md:text-xl mb-6">Tu carrito está vacío</p>
                <router-link to="/" class="bg-white text-black px-6 md:px-8 py-3 rounded-full font-bold hover:bg-primary hover:text-white transition-colors text-sm md:text-base">
                    VOLVER A LA TIENDA
                </router-link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-4">
                    <div v-for="item in cartStore.items" :key="item.id"
                         class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-black/30 p-4 rounded-xl border border-white/5 hover:border-white/20 transition-all relative">

                        <div class="flex gap-4 w-full sm:w-auto flex-1">
                            <img :src="item.imageRoute" :alt="item.name" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg bg-gray-800 shrink-0" />

                            <div class="flex flex-col justify-center pr-8 sm:pr-0">
                                <h3 class="font-bold text-base sm:text-lg uppercase line-clamp-2">{{ item.name }}</h3>
                                <p class="text-primary font-semibold">{{ formatCurrency(item.price) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto mt-2 sm:mt-0 gap-4">
                            <div class="flex items-center bg-black/50 rounded-lg p-1 border border-white/10 shrink-0">
                                <button @click="cartStore.decrementQuantity(item.id)"
                                        class="w-8 h-8 flex items-center justify-center hover:text-primary transition-colors">
                                    <span class="text-xl">-</span>
                                </button>
                                <span class="w-10 text-center font-bold text-sm sm:text-base">{{ item.quantity }}</span>
                                <button @click="handleAddQty(item)"
                                        class="w-8 h-8 flex items-center justify-center hover:text-primary transition-colors">
                                    <span class="text-xl">+</span>
                                </button>
                            </div>

                            <div class="text-right hidden sm:block min-w-[80px]">
                                <p class="text-xs sm:text-sm opacity-50">Subtotal</p>
                                <p class="font-bold">{{ formatCurrency(item.price * item.quantity) }}</p>
                            </div>
                        </div>

                        <button @click="cartStore.removeFromCart(item.id)"
                                class="absolute top-4 right-4 sm:static sm:ml-2 p-2 text-white/30 hover:text-red-500 transition-colors">
                            <i class="pi pi-trash"></i>
                        </button>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button @click="cartStore.clearCart" class="text-sm text-white/40 hover:text-white transition-colors underline decoration-dotted">
                            Vaciar carrito
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white text-black p-5 md:p-6 rounded-2xl lg:sticky lg:top-24 shadow-xl">
                        <h2 class="text-xl font-black mb-6 uppercase italic">Resumen</h2>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-sm md:text-base">
                                <span>Productos ({{ cartStore.totalItems() }})</span>
                                <span>{{ formatCurrency(cartStore.totalPrice()) }}</span>
                            </div>
                            <div class="flex justify-between text-sm opacity-80">
                                <span>Gastos de envío</span>
                                <span class="text-emerald-600 font-bold">GRATIS</span>
                            </div>

                            <div v-if="havePromoCode" class="flex justify-between text-sm">
                                <span>Descuento</span>
                                <span class="text-amber-500 font-bold">-20%</span>
                            </div>

                            <div class="mt-6 mb-6">
                                <label for="promo" class="text-xs sm:text-sm font-bold uppercase tracking-wider opacity-60 mb-2 block">
                                    ¿Tienes un código de descuento?
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="promo"
                                        type="text"
                                        placeholder="CÓDIGO"
                                        v-model="promoCode"
                                        class="flex-1 bg-black/5 border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all uppercase font-medium"
                                    />
                                    <button
                                        @click="applyPromo"
                                        class="bg-black text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-primary transition-colors uppercase shrink-0"
                                    >
                                        Aplicar
                                    </button>
                                </div>
                            </div>

                            <hr class="border-black/10" />

                            <div v-if="!havePromoCode" class="flex justify-between items-center text-xl font-black">
                                <span>TOTAL</span>
                                <span>{{ formatCurrency(cartStore.totalPrice()) }}</span>
                            </div>
                            <div v-else class="flex flex-col gap-1">
                                <div class="flex justify-between items-center text-sm opacity-50 line-through">
                                    <span>Subtotal</span>
                                    <span>{{ formatCurrency(cartStore.totalPrice()) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-2xl font-black text-emerald-500">
                                    <span>TOTAL</span>
                                    <span>{{ formatCurrency(cartStore.totalWithDiscount()) }}</span>
                                </div>
                            </div>
                        </div>

                        <button @click="goToCheckout"
                                class="w-full bg-primary text-white py-3.5 md:py-4 rounded-xl font-bold text-base md:text-lg hover:bg-primary-hover active:scale-[0.98] transition-all shadow-lg flex items-center justify-center gap-2">
                            FINALIZAR COMPRA
                            <i class="pi pi-arrow-right"></i>
                        </button>

                        <p class="text-[10px] text-center mt-4 opacity-50 uppercase tracking-widest font-semibold">
                            Pagos seguros y devoluciones gratuitas
                        </p>
                    </div>
                </div>

            </div>
        </main>

        <FooterBase class="mt-auto" />
    </div>
</template>
