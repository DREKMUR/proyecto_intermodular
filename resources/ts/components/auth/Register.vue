<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import HeaderBase from "@/components/HeaderBase.vue";
import axios from 'axios';
import type {ApiError, RegisterForm} from "@/types.ts";

const auth = useAuthStore();
const router = useRouter();

const form = reactive<RegisterForm>({
    name: '',
    email: '',
    password: ''
});

const errorMessage = ref<string | null>(null);
const loading = ref<boolean>(false);

const handleSubmit = async (): Promise<void> => {
    loading.value = true;
    errorMessage.value = null;

    try {
        const { data } = await axios.post('/api/register', form);

        auth.token = data.access_token;
        auth.user = data.user;

        localStorage.setItem('auth_token', data.access_token);
        localStorage.setItem('user', JSON.stringify(data.user));

        axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;

        if (auth.isAdmin) {
            await router.push({ name: 'admin-dashboard' });
        } else {
            await router.push({ name: 'home' });
        }
    } catch (e: unknown) {
        const error = e as ApiError;
        errorMessage.value = error.response?.data?.message ?? 'Error al registrar usuario';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-bg antialiased">
        <HeaderBase />

        <main class="flex-1 flex items-center justify-center p-6 mt-8">
            <div class="w-full max-w-md bg-[var(--p-surface-0)] rounded-[var(--p-content-border-radius)] shadow-2xl border border-[var(--p-content-border-color)] overflow-hidden">
                <div class="p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-[var(--p-surface-900)] tracking-tight">Crear Cuenta</h1>
                        <p class="text-[var(--p-text-muted-color)] mt-2 text-sm">Regístrate para comenzar</p>
                    </div>

                    <form @submit.prevent="handleSubmit" class="flex flex-col gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-[var(--p-surface-800)]">Nombre</label>
                            <div class="relative">
                                <i class="pi pi-user absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--p-text-muted-color)]"></i>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Tu nombre completo"
                                    class="w-full pl-10 pr-4 py-2.5 bg-[var(--p-surface-50)] border border-[var(--p-content-border-color)] rounded-[var(--p-content-border-radius)] text-[var(--p-text-color)] placeholder:text-[var(--p-surface-400)] focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-[var(--p-surface-800)]">Email</label>
                            <div class="relative">
                                <i class="pi pi-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--p-text-muted-color)]"></i>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="tu@email.com"
                                    class="w-full pl-10 pr-4 py-2.5 bg-[var(--p-surface-50)] border border-[var(--p-content-border-color)] rounded-[var(--p-content-border-radius)] text-[var(--p-text-color)] placeholder:text-[var(--p-surface-400)] focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-[var(--p-surface-800)]">Contraseña</label>
                            <div class="relative">
                                <i class="pi pi-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--p-text-muted-color)]"></i>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    required
                                    placeholder="••••••••"
                                    minlength="8"
                                    class="w-full pl-10 pr-4 py-2.5 bg-[var(--p-surface-50)] border border-[var(--p-content-border-color)] rounded-[var(--p-content-border-radius)] text-[var(--p-text-color)] placeholder:text-[var(--p-surface-400)] focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                />
                            </div>
                        </div>

                        <div v-if="errorMessage" class="flex items-center gap-2 p-3 mt-1 rounded-[var(--p-content-border-radius)] bg-error/10 border border-error/20 text-error text-sm font-medium animate-fade-in">
                            <i class="pi pi-exclamation-circle text-base"></i>
                            <span>{{ errorMessage }}</span>
                        </div>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full py-3 px-4 mt-3 bg-primary hover:bg-primary-hover active:bg-primary-active text-[var(--p-primary-contrast-color)] font-semibold rounded-[var(--p-content-border-radius)] transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-[var(--p-surface-0)]"
                        >
                            <i v-if="loading" class="pi pi-spinner pi-spin"></i>
                            <span>{{ loading ? 'Creando cuenta...' : 'Registrarse' }}</span>
                        </button>

                        <div class="text-center mt-2">
                            <router-link :to="{ name: 'login' }" class="text-sm text-primary hover:text-primary-hover font-medium transition-colors">
                                ¿Ya tienes una cuenta? Inicia sesión
                            </router-link>
                        </div>
                    </form>
                </div>
            </div>
        </main>
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
