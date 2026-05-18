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
        toast.add({severity: 'warn', summary: 'Necesitas iniciar sesion para proceder con el pago.', life: 3000});
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
        toast.add({severity: 'success', summary: 'Codigo de descuento añadido con exito.', life: 3000});
        havePromoCode.value = true;
        cartStore.discountPercent = 20;
    } else {
        toast.add({severity: 'warn', summary: 'Codigo de descuento no reconocido.', life: 3000});
    }
};
</script>

<template>
    <div class="min-h-screen bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />

        <main class="max-w-6xl mx-auto p-6">
            <h1 class="text-3xl font-bold mb-8 italic">TU CESTA</h1>

            <div v-if="cartStore.items.length === 0" class="flex flex-col items-center justify-center py-20 bg-black/20 rounded-2xl border border-white/10">
                <i class="pi pi-shopping-cart text-6xl mb-4 opacity-20"></i>
                <p class="text-xl mb-6">Tu carrito está vacío</p>
                <router-link to="/" class="bg-white text-black px-8 py-3 rounded-full font-bold hover:bg-primary hover:text-white transition-colors">
                    VOLVER A LA TIENDA
                </router-link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-4">
                    <div v-for="item in cartStore.items" :key="item.id"
                         class="flex items-center gap-4 bg-black/30 p-4 rounded-xl border border-white/5 hover:border-white/20 transition-all">

                        <img :src="item.imageRoute" :alt="item.name" class="w-20 h-20 object-cover rounded-lg bg-gray-800" />

                        <div class="flex-1">
                            <h3 class="font-bold text-lg uppercase">{{ item.name }}</h3>
                            <p class="text-primary font-semibold">{{ formatCurrency(item.price) }}</p>
                        </div>

                        <div class="flex items-center bg-black/50 rounded-lg p-1 border border-white/10">
                            <button @click="cartStore.decrementQuantity(item.id)"
                                    class="w-8 h-8 flex items-center justify-center hover:text-primary transition-colors">
                                <span class="text-xl">-</span>
                            </button>
                            <span class="w-10 text-center font-bold">{{ item.quantity }}</span>
                            <button @click="handleAddQty(item)"
                                    class="w-8 h-8 flex items-center justify-center hover:text-primary transition-colors">
                                <span class="text-xl">+</span>
                            </button>
                        </div>

                        <div class="text-right ml-4 hidden sm:block">
                            <p class="text-sm opacity-50">Subtotal</p>
                            <p class="font-bold">{{ formatCurrency(item.price * item.quantity) }}</p>
                        </div>

                        <button @click="cartStore.removeFromCart(item.id)"
                                class="ml-4 p-2 text-white/30 hover:text-red-500 transition-colors">
                            <i class="pi pi-trash"></i>
                        </button>
                    </div>

                    <button @click="cartStore.clearCart" class="text-sm text-white/40 hover:text-white transition-colors underline decoration-dotted">
                        Vaciar carrito
                    </button>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white text-black p-6 rounded-2xl sticky top-24">
                        <h2 class="text-xl font-black mb-6 uppercase italic">Resumen</h2>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span>Productos ({{ cartStore.totalItems() }})</span>
                                <span>{{ formatCurrency(cartStore.totalPrice()) }}</span>
                            </div>
                            <div class="flex justify-between text-sm opacity-60">
                                <span>Gastos de envío</span>
                                <span class="text-green-600 font-bold">GRATIS</span>
                            </div>
                            <div v-if="havePromoCode" class="flex justify-between text-sm opacity-60">
                                <span>Descuento</span>
                                <span class="text-yellow-600 font-bold">-20%</span>
                            </div>
                            <div class="mt-4 mb-6">
                                <label for="promo" class="text-sm font-bold uppercase tracking-wider opacity-50 mb-1 block">
                                    ¿Tienes un código de descuento?
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="promo"
                                        type="text"
                                        placeholder="CÓDIGO"
                                        v-model="promoCode"
                                        class="flex-1 bg-black/5 border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary transition-colors uppercase font-medium"
                                    />
                                    <button
                                        @click="applyPromo"
                                        class="bg-black text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-primary transition-colors uppercase"
                                    >
                                        Aplicar
                                    </button>
                                </div>
                            </div>
                            <hr class="border-black/10" />
                            <div v-if="!havePromoCode" class="flex justify-between text-xl font-black">
                                <span>TOTAL</span>
                                <span>{{ formatCurrency(cartStore.totalPrice()) }}</span>
                            </div>
                            <div v-else class="flex flex-col justify-between text-xl font-black gap-5">
                                <div class="flex justify-between">
                                    <span>TOTAL</span>
                                    <span>{{ formatCurrency(cartStore.totalPrice()) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>DESCONTADO</span>
                                    <span>{{ formatCurrency(cartStore.totalWithDiscount()) }}</span>
                                </div>
                            </div>
                        </div>

                        <button @click="goToCheckout"
                                class="w-full bg-primary text-white py-4 rounded-xl font-bold text-lg hover:scale-[1.02] active:scale-95 transition-all shadow-lg flex items-center justify-center gap-2">
                            FINALIZAR COMPRA
                            <i class="pi pi-arrow-right"></i>
                        </button>

                        <p class="text-[10px] text-center mt-4 opacity-50 uppercase tracking-widest">
                            Pagos seguros y devoluciones gratuitas
                        </p>
                    </div>
                </div>

            </div>
        </main>
        <FooterBase />
    </div>
</template>
