<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "primevue/usetoast";
import { useCartStore } from "@/stores/cart";
import { useAuthStore } from "@/stores/auth.ts";
import HeaderBase from "@/components/HeaderBase.vue";
import axios from "axios";
import FooterBase from "@/components/FooterBase.vue";

const cartStore = useCartStore();
const router = useRouter();
const toast = useToast();
const authStore = useAuthStore();

if (cartStore.totalItems() === 0)
    router.push("/");
else if(!authStore.isLoggedIn)
    router.push('login');

const shippingAddress = ref<string>(authStore.user?.shipping_address || "");
const billingAddress = ref<string>(authStore.user?.billing_address || "");
const sameAsShipping = ref<boolean>(
    !!authStore.user?.shipping_address &&
    authStore.user.shipping_address === authStore.user.billing_address
);

const isProcessing = ref<boolean>(false);
const isConfirmed = ref<boolean>(false);

const creditCard = ref({
    number: "",
    expiry: "",
    cvv: ""
});

const shippingCost = ref<number>(0);
const hasDiscount = computed(() => cartStore.discountPercent > 0);

const finalTotal = computed<number>(() => {
    if (hasDiscount.value) {
        return cartStore.totalWithDiscount();
    }
    return cartStore.totalPrice() + shippingCost.value;
});

watch([shippingAddress, sameAsShipping], () => {
    if (sameAsShipping.value) {
        billingAddress.value = shippingAddress.value;
    }
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const handlePayment = async () => {
    if (!authStore.user?.name || !shippingAddress.value || !billingAddress.value || !creditCard.value.number || !creditCard.value.expiry || !creditCard.value.cvv) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Por favor, rellena todos los campos obligatorios.', life: 3000 });
        return;
    }

    isProcessing.value = true;

    const orderData = {
        shipping_address: shippingAddress.value,
        billing_address: billingAddress.value,
        discount_percent: cartStore.discountPercent,
        items: cartStore.items.map(item => ({
            id: item.id,
            quantity: item.quantity
        }))
    };

    try{
        const response = await axios.post('/api/orders', orderData, {
            headers: {
                Authorization: `Bearer ${authStore.token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        });

        if (response.data.success) {
            isConfirmed.value = true;
            toast.add({
                severity: 'success',
                summary: '¡Compra completada!',
                detail: response.data.message || 'Tu pedido ha sido procesado con éxito.',
                life: 3000
            });
            finishAndClear();
        }
    } catch(error: any) {
        console.error('Error procesando el pedido: ' ,error);

        toast.add({
            severity: 'error',
            summary: 'Error en el Pedido',
            detail: error.response?.data?.message || 'Hubo un problema al procesar tu pedido. Inténtalo de nuevo.',
            life: 5000
        });
    } finally {
        isProcessing.value = false;
    }
};

const finishAndClear = () => {
    cartStore.clearCart();
    cartStore.discountPercent = 0;
    setTimeout(() => window.dispatchEvent(new CustomEvent('check-pending-opinion')), 1000);
};
</script>

<template>
    <div class="min-h-screen bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />

        <main class="max-w-6xl mx-auto p-6">

            <div v-if="isProcessing" class="flex flex-col items-center justify-center py-32 text-center">
                <i class="pi pi-spin pi-spinner text-6xl text-primary mb-4"></i>
                <h2 class="text-2xl font-bold uppercase tracking-wider">Procesando tu pago...</h2>
                <p class="opacity-60 mt-2">No cierres ni refresques esta ventana.</p>
            </div>

            <div v-else-if="isConfirmed" class="max-w-3xl mx-auto bg-black/40 border border-white/10 p-8 rounded-2xl shadow-2xl">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center mx-auto mb-4 border border-green-500/30">
                        <i class="pi pi-check text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-black italic uppercase">¡PAGO CONFIRMADO!</h1>
                    <p class="text-sm opacity-60 mt-1">Identificación Fiscal / Doc: {{ authStore.user?.document_id }}</p>
                </div>

                <hr class="border-white/10 my-6" />

                <h3 class="font-bold text-lg uppercase tracking-wider mb-4 text-primary">Resumen de la Orden</h3>
                <div class="space-y-3 bg-black/20 p-4 rounded-xl border border-white/5 mb-6">
                    <div v-for="item in cartStore.items" :key="item.id" class="flex justify-between items-center text-sm">
                        <div>
                            <span class="font-bold uppercase">{{ item.name }}</span>
                            <span class="opacity-50 text-xs block">Cantidad: {{ item.quantity }} x {{ formatCurrency(item.price) }}</span>
                        </div>
                        <span class="font-semibold">{{ formatCurrency(item.price * item.quantity) }}</span>
                    </div>

                    <hr class="border-white/5 my-2" />

                    <div v-if="hasDiscount" class="flex justify-between text-xs text-yellow-500 font-bold">
                        <span>Descuento Aplicado</span>
                        <span>-{{ cartStore.discountPercent }}%</span>
                    </div>
                    <div class="flex justify-between text-xs opacity-60">
                        <span>Gastos de envío</span>
                        <span>GRATIS</span>
                    </div>
                    <div class="flex justify-between text-xl font-black pt-2 text-white">
                        <span>TOTAL PAGADO</span>
                        <span class="text-primary">{{ formatCurrency(finalTotal) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm bg-white/5 p-4 rounded-xl border border-white/5">
                    <div>
                        <h4 class="font-bold uppercase text-xs tracking-wider opacity-50 mb-2">Dirección de Envío</h4>
                        <p class="font-bold text-base">{{ authStore.user?.name }}</p>
                        <p>{{ shippingAddress }}</p>
                        <p class="opacity-60 mt-1"><i class="pi pi-phone text-xs mr-1"></i> {{ authStore.user?.phone }}</p>
                    </div>
                    <div>
                        <h4 class="font-bold uppercase text-xs tracking-wider opacity-50 mb-2">Dirección de Facturación</h4>
                        <p v-if="sameAsShipping" class="opacity-60 italic text-slate-400 mt-2">Misma que la dirección de envío</p>
                        <div v-else>
                            <p class="font-bold text-base">{{ authStore.user?.name }}</p>
                            <p>{{ billingAddress }}</p>
                        </div>
                    </div>
                </div>

                <button @click="router.push('/')"
                        class="w-full mt-8 bg-white text-black py-4 rounded-xl font-bold text-lg hover:bg-primary hover:text-white transition-colors uppercase tracking-wider">
                    Volver a la tienda
                </button>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <h1 class="text-3xl font-bold mb-4 italic uppercase">Finalizar Compra</h1>

                    <div class="bg-black/30 p-6 rounded-2xl border border-white/5 space-y-4">
                        <h2 class="text-xl font-bold uppercase tracking-wider text-slate-100 border-b border-white/10 pb-2">
                            <i class="pi pi-map-marker mr-2"></i> Datos de Envío
                        </h2>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="text-xs uppercase opacity-60 mb-1 font-bold">Nombre Completo</label>
                                    <input type="text" :value="authStore.user?.name" disabled class="bg-black/20 border border-white/10 rounded-lg p-2.5 text-sm opacity-50 cursor-not-allowed"/>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-xs uppercase opacity-60 mb-1 font-bold">Teléfono de Contacto</label>
                                    <input type="text" :value="authStore.user?.phone" disabled class="bg-black/20 border border-white/10 rounded-lg p-2.5 text-sm opacity-50 cursor-not-allowed"/>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-xs uppercase opacity-60 mb-1 font-bold">Dirección de Envío Completa *</label>
                                <input type="text" v-model="shippingAddress" required placeholder="Calle, Número, Piso, Código Postal, Ciudad, Provincia" class="bg-black/40 border border-white/10 rounded-lg p-2.5 focus:outline-none focus:border-primary text-sm"/>
                            </div>
                        </div>
                    </div>

                    <div class="bg-black/30 p-6 rounded-2xl border border-white/5 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-2">
                            <h2 class="text-xl font-bold uppercase tracking-wider text-slate-100">
                                <i class="pi pi-file mr-2"></i> Datos de Facturación
                            </h2>
                            <label class="flex items-center gap-2 text-xs uppercase font-bold cursor-pointer opacity-80 hover:opacity-100">
                                <input type="checkbox" v-model="sameAsShipping" class="accent-primary w-4 h-4 rounded" />
                                Igual que envío
                            </label>
                        </div>

                        <div v-if="!sameAsShipping" class="flex flex-col gap-4 pt-2">
                            <div class="flex flex-col">
                                <label class="text-xs uppercase opacity-60 mb-1 font-bold">Dirección de Facturación Completa *</label>
                                <input type="text" v-model="billingAddress" required placeholder="Calle, Número, Piso, Código Postal, Ciudad, Provincia" class="bg-black/40 border border-white/10 rounded-lg p-2.5 focus:outline-none focus:border-primary text-sm"/>
                            </div>
                        </div>
                    </div>

                    <div class="bg-black/30 p-6 rounded-2xl border border-white/5 space-y-4">
                        <h2 class="text-xl font-bold uppercase tracking-wider text-slate-100 border-b border-white/10 pb-2">
                            <i class="pi pi-credit-card mr-2"></i> Pago con Tarjeta (Ficticio)
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col md:col-span-2">
                                <label class="text-xs uppercase opacity-60 mb-1 font-bold">Número de Tarjeta *</label>
                                <input type="text" v-model="creditCard.number" maxlength="16" placeholder="0000 0000 0000 0000" class="bg-black/40 border border-white/10 rounded-lg p-2.5 focus:outline-none focus:border-primary text-sm tracking-widest"/>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex flex-col">
                                    <label class="text-xs uppercase opacity-60 mb-1 font-bold">Caducidad *</label>
                                    <input type="text" v-model="creditCard.expiry" maxlength="5" placeholder="MM/AA" class="bg-black/40 border border-white/10 rounded-lg p-2.5 text-center focus:outline-none focus:border-primary text-sm"/>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-xs uppercase opacity-60 mb-1 font-bold">CVV *</label>
                                    <input type="password" v-model="creditCard.cvv" maxlength="3" placeholder="123" class="bg-black/40 border border-white/10 rounded-lg p-2.5 text-center focus:outline-none focus:border-primary text-sm"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white text-black p-6 rounded-2xl sticky top-24 shadow-xl">
                        <h2 class="text-xl font-black mb-4 uppercase italic">Tu Pedido</h2>

                        <div class="max-h-40 overflow-y-auto space-y-2 pr-1 mb-4 border-b border-black/10 pb-4">
                            <div v-for="item in cartStore.items" :key="item.id" class="flex justify-between text-xs font-medium">
                                <span class="truncate max-w-45 font-bold uppercase">{{ item.name }} (x{{ item.quantity }})</span>
                                <span>{{ formatCurrency(item.price * item.quantity) }}</span>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm mb-6">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>{{ formatCurrency(cartStore.totalPrice()) }}</span>
                            </div>
                            <div v-if="hasDiscount" class="flex justify-between text-xs text-yellow-600 font-bold">
                                <span>Descuento ({{ cartStore.discountPercent }}%)</span>
                                <span>-{{ formatCurrency(cartStore.totalPrice() * (cartStore.discountPercent / 100)) }}</span>
                            </div>
                            <div class="flex justify-between text-xs opacity-60">
                                <span>Envío</span>
                                <span class="text-green-600 font-bold">GRATIS</span>
                            </div>
                            <hr class="border-black/10" />
                            <div class="flex justify-between text-lg font-black">
                                <span>TOTAL</span>
                                <span class="text-primary">{{ formatCurrency(finalTotal) }}</span>
                            </div>
                        </div>

                        <button @click="handlePayment"
                                class="w-full bg-primary text-white py-4 rounded-xl font-bold text-lg hover:scale-[1.02] active:scale-95 transition-all shadow-lg flex items-center justify-center gap-2 uppercase tracking-wide">
                            Pagar Ahora
                            <i class="pi pi-lock"></i>
                        </button>
                    </div>
                </div>

            </div>
        </main>
        <FooterBase />
    </div>
</template>
