<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import Button from '@volt/Button.vue'

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async (): Promise<void> => {
    await authStore.logout();
    router.push({ name: 'login' });
};
</script>

<template>
    <div class="z-50 flex px-10 font-semibold items-center justify-between py-6 bg-linear-to-l from-secondary to-black text-white sticky top-0 w-full shadow-xl">

        <RouterLink :to="{ name: 'home' }">
            <img src="/public/images/logo.png" alt="Logo" class="w-40 hover:scale-110 transition-transform" />
        </RouterLink>

        <div class="flex items-center justify-center gap-5">
            <RouterLink
                v-if="$route.name !== 'catalog'"
                :to="{ name: 'catalog' }"
                class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
            >
                <i class="pi pi-car"></i>
                Catálogo
            </RouterLink>

            <RouterLink
                v-if="$route.name !== 'contactUs'"
                :to="{ name: 'contactUs' }"
                class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
            >
                <i class="pi pi-user"></i>
                Contáctanos
            </RouterLink>

            <RouterLink v-if="$route.name !== 'cart'" :to="{ name: 'cart' }" class="relative inline-block ml-4">
                <i class="pi pi-shopping-cart hover:scale-125 scale-110 transition-transform cursor-pointer hover:bg-slate-200 p-3 rounded-full hover:text-secondary hover:shadow-md" />
            </RouterLink>

            <RouterLink
                v-if="$route.name !== 'listTickets' && authStore.isAdmin"
                :to="{ name: 'listTickets' }"
                class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md flex items-center gap-1 justify-center"
            >
                <i class="pi pi-ticket"></i>
                Gestionar Tickets
            </RouterLink>
        </div>

        <!-- Autenticación -->
        <div class="flex items-center gap-6">
            <template v-if="!authStore.isLoggedIn">
                <RouterLink
                    v-if="$route.name !== 'login'"
                    :to="{ name: 'login' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
                >
                    Iniciar sesión
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'register'"
                    :to="{ name: 'register' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
                >
                    Registro
                </RouterLink>
            </template>

            <template v-else>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-white">Hola, {{ authStore.user?.name }}</span>

                    <Button
                        @click="handleLogout"
                        class="rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
                    >
                        Salir
                    </Button>
                </div>
            </template>
        </div>
    </div>
</template>
