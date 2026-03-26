<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import Button from '@volt/Button.vue'

const auth = useAuthStore();
const router = useRouter();

const handleLogout = async (): Promise<void> => {
    await auth.logout();
    router.push({ name: 'login' });
};
</script>

<template>
    <div class="z-50 flex px-10 font-semibold items-center justify-between py-6 bg-gradient-to-l from-secondary to-black text-white sticky top-0 w-full shadow-xl">

        <RouterLink :to="{ name: 'home' }">
            <img src="/public/images/logo.png" alt="Logo" class="w-40 hover:scale-110 transition-transform" />
        </RouterLink>

        <div class="flex items-center gap-6">
            <template v-if="!auth.isLoggedIn">
                <RouterLink
                    v-if="$route.name !== 'login'"
                    :to="{ name: 'login' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all"
                >
                    Iniciar sesión
                </RouterLink>

                <RouterLink
                    v-if="$route.name !== 'register'"
                    :to="{ name: 'register' }"
                    class="hover:bg-slate-200 rounded-sm px-4 py-2 hover:text-secondary-hover transition-all"
                >
                    Registro
                </RouterLink>
            </template>

            <template v-else>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-light text-slate-300">Hola, {{ auth.user?.name }}</span>

                    <Button
                        @click="handleLogout"
                        class="hover:text-secondary-hover transition-colors uppercase"
                    >
                        Salir
                    </Button>
                </div>
            </template>

            <RouterLink v-if="$route.name !== 'cart'" :to="{ name: 'cart' }" class="relative inline-block ml-4">
                <i class="pi pi-shopping-cart hover:scale-125 scale-110 transition-transform cursor-pointer" />
            </RouterLink>
        </div>
    </div>
</template>
