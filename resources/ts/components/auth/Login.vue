<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import HeaderBase from "@/components/HeaderBase.vue";
import type {ApiError, LoginForm} from "@/types.ts";

const auth = useAuthStore();
const router = useRouter();

const form = reactive<LoginForm>({
    email: '',
    password: ''
});

const errorMessage = ref<string | null>(null);
const loading = ref<boolean>(false);

const handleSubmit = async (): Promise<void> => {
    loading.value = true;
    errorMessage.value = null;

    try {
        await auth.login(form);

        if (auth.isAdmin) {
            await router.push({ name: 'admin-dashboard' });
        } else {
            await router.push({ name: 'home' });
        }
    } catch (e: unknown) {
        const error = e as ApiError;
        errorMessage.value = error.response?.data?.message ?? 'Error al iniciar sesión';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
        <HeaderBase />

        <div class="flex-1 flex items-center justify-center p-6 text-slate-700">
            <div class="w-full max-w-md bg-slate-100 rounded shadow-2xl border">
                <div class="p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-slate-900">Bienvenido</h1>
                        <p class="mt-2 text-sm">Ingresa a tu cuenta para continuar</p>
                    </div>

                    <form @submit.prevent="handleSubmit" class="flex flex-col gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold ">Correo electrónico</label>
                            <div class="relative">
                                <i class="pi pi-envelope absolute left-3.5 top-1/2 -translate-y-1/2 "></i>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="example@example.com"
                                    class="w-full pl-10 pr-4 py-2.5 focus:bg-slate-200 hover:bg-slate-200 border border-slate-300 rounded  placeholder:/60 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold ">Contraseña</label>
                            <div class="relative">
                                <i class="pi pi-lock absolute left-3.5 top-1/2 -translate-y-1/2 "></i>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    required
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-4 py-2.5 focus:bg-slate-200 hover:bg-slate-200 border border-slate-300 rounded  placeholder:/60 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                />
                            </div>
                        </div>

                        <div v-if="errorMessage" class="flex items-center gap-2 p-3 mt-1 rounded bg-error/10 border border-error/20 text-error text-sm font-medium animate-fade-in">
                            <i class="pi pi-exclamation-circle text-base"></i>
                            <span>{{ errorMessage }}</span>
                        </div>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full py-3 px-4 mt-3 bg-primary hover:bg-primary-hover active:bg-primary-active text-white hover:cursor-pointer font-semibold rounded transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        >
                            <i v-if="loading" class="pi pi-spinner pi-spin"></i>
                            <span>{{ loading ? 'Autenticando...' : 'Entrar' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
</template>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
}
</style>
