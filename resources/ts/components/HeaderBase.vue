<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import Button from '@volt/Button.vue'

const authStore = useAuthStore();
const router = useRouter();
const isMenuOpen = ref(false);

const handleLogout = async (): Promise<void> => {
    isMenuOpen.value = false;
    await authStore.logout();
    router.push({ name: 'login' });
};

const toggleMenu = (): void => {
    isMenuOpen.value = !isMenuOpen.value;
};

const closeMenu = (): void => {
    isMenuOpen.value = false;
};
</script>

<template>
    <div class="z-50 bg-linear-to-l from-secondary to-black text-white sticky top-0 w-full shadow-xl">
        <div class="flex px-10 font-semibold items-center justify-between py-6">
            <RouterLink :to="{ name: 'home' }" @click="closeMenu">
                <img src="/public/images/logo.avif" alt="Logo" class="w-40 hover:scale-110 transition-transform" />
            </RouterLink>

            <div class="hidden md:flex items-center justify-center gap-5">
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

                <RouterLink
                    v-if="$route.name !== 'whereWeAre'"
                    :to="{ name: 'whereWeAre' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
                >
                    <i class="pi pi-map"></i>
                    Donde encontrarnos
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'FAQ'"
                    :to="{ name: 'FAQ' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
                >
                    <i class="pi pi-question-circle"></i>
                    FAQs
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

            <div class="hidden md:flex items-center gap-6">
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

            <button @click="toggleMenu" class="md:hidden text-white focus:outline-none cursor-pointer p-2">
                <i :class="isMenuOpen ? 'pi pi-times text-2xl' : 'pi pi-bars text-2xl'"></i>
            </button>
        </div>

        <div v-if="isMenuOpen" class="md:hidden bg-black/95 border-t border-white/10 px-10 py-6 absolute left-0 right-0 top-full shadow-2xl flex flex-col gap-6">
            <div class="flex flex-col gap-4 font-semibold">
                <RouterLink
                    v-if="$route.name !== 'catalog'"
                    :to="{ name: 'catalog' }"
                    @click="closeMenu"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all flex items-center gap-2"
                >
                    <i class="pi pi-car"></i>
                    Catálogo
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'contactUs'"
                    :to="{ name: 'contactUs' }"
                    @click="closeMenu"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all flex items-center gap-2"
                >
                    <i class="pi pi-user"></i>
                    Contáctanos
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'whereWeAre'"
                    :to="{ name: 'whereWeAre' }"
                    @click="closeMenu"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all flex items-center gap-2"
                >
                    <i class="pi pi-map"></i>
                    Donde encontrarnos
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'FAQ'"
                    :to="{ name: 'FAQ' }"
                    @click="closeMenu"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all flex items-center gap-2"
                >
                    <i class="pi pi-question-circle"></i>
                    FAQs
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'cart'"
                    :to="{ name: 'cart' }"
                    @click="closeMenu"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all flex items-center gap-2"
                >
                    <i class="pi pi-shopping-cart" />
                    Carrito
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'listTickets' && authStore.isAdmin"
                    :to="{ name: 'listTickets' }"
                    @click="closeMenu"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all flex items-center gap-2"
                >
                    <i class="pi pi-ticket"></i>
                    Gestionar Tickets
                </RouterLink>
            </div>

            <hr class="border-white/10" />

            <div class="flex flex-col gap-4 font-semibold">
                <template v-if="!authStore.isLoggedIn">
                    <RouterLink
                        v-if="$route.name !== 'login'"
                        :to="{ name: 'login' }"
                        @click="closeMenu"
                        class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all text-center block"
                    >
                        Iniciar sesión
                    </RouterLink>

                    <RouterLink
                        v-if="$route.name !== 'register'"
                        :to="{ name: 'register' }"
                        @click="closeMenu"
                        class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all text-center block"
                    >
                        Registro
                    </RouterLink>
                </template>

                <template v-else>
                    <div class="flex flex-col items-center gap-4">
                        <span class="text-sm font-semibold text-white">Hola, {{ authStore.user?.name }}</span>

                        <Button
                            @click="handleLogout"
                            class="w-full rounded-sm px-4 py-2 hover:text-secondary-hover transition-all hover:shadow-md"
                        >
                            Salir
                        </Button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
