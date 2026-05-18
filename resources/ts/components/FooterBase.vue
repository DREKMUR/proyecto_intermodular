<script setup lang="ts">

</script>

<template>
    <footer class="w-full bg-linear-to-l from-secondary to-black text-white font-semibold py-12 px-10 shadow-inner mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 items-start justify-between gap-8 border-b pb-8">

            <!-- Logo / Branding y Copyright -->
            <div class="flex flex-col items-center md:items-start gap-2">
                <RouterLink :to="{ name: 'home' }">
                    <img src="/public/images/logo.png" alt="Logo" class="w-36 hover:scale-105 transition-transform" />
                </RouterLink>
                <p class="text-sm text-slate-100 mt-2 text-center md:text-left">
                    © 2026 DeMuFe®. Todos los derechos reservados.
                </p>
            </div>

            <!-- Navegación -->
            <div class="flex flex-col items-center gap-3">
                <span class="text-slate-100 uppercase tracking-wider text-xs">Navegación</span>
                <RouterLink
                    :to="{ name: 'whereWeAre' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md flex items-center gap-2 text-sm"
                >
                    <i class="pi pi-map"></i>
                    Donde encontrarnos
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'contactUs'"
                    :to="{ name: 'contactUs' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md flex items-center gap-2 text-sm"
                >
                    <i class="pi pi-user"></i>
                    Contáctanos
                </RouterLink>
            </div>

            <!-- Condiciones de Envío -->
            <div class="flex flex-col items-center gap-2 text-center">
                <span class="text-slate-100 uppercase tracking-wider text-xs mb-1">Condiciones de envío</span>
                <div class="text-xs text-slate-200 space-y-1 font-normal">
                    <p class="font-semibold text-white">📦 Envíos a toda España</p>
                    <p>Entrega en 24/48h para accesorios.</p>
                    <p>Recogida en taller para personalizaciones.</p>
                </div>
            </div>

            <!-- Formas de Pago Aceptadas -->
            <div class="flex flex-col items-center md:items-end gap-3">
                <span class="text-slate-100 uppercase tracking-wider text-xs">Formas de pago</span>
                <div class="flex items-center gap-4 text-2xl text-slate-100">
                    <i class="pi pi-credit-card hover:text-white transition-colors" title="Tarjetas de Crédito / Débito"></i>
                    <i class="pi pi-paypal hover:text-white transition-colors" title="PayPal"></i>
                    <i class="pi pi-wallet hover:text-white transition-colors" title="Apple Pay / Google Pay"></i>
                </div>
                <span class="text-xs text-slate-100">Pago 100% seguro garantizado</span>
            </div>

        </div>

        <!-- Enlaces Legales -->
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-center gap-6 mt-6 text-sm text-slate-100">
            <a href="#" class="hover:text-white transition-colors underline decoration-slate-100 hover:decoration-white">
                Aviso juridico
            </a>
            <span class="hidden sm:inline text-slate-100">•</span>
            <a href="#" class="hover:text-white transition-colors underline decoration-slate-100 hover:decoration-white">
                Política de cookies
            </a>
            <span class="hidden sm:inline text-slate-100">•</span>
            <a href="#" class="hover:text-white transition-colors underline decoration-slate-100 hover:decoration-white">
                Política de confidencialidad
            </a>
            <span class="hidden sm:inline text-slate-100">•</span>
            <a href="#" class="hover:text-white transition-colors underline decoration-slate-100 hover:decoration-white">
                Condicions de envío
            </a>
        </div>
    </footer>
</template>
