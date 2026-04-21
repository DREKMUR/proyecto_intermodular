<script setup lang="ts">
import { reactive, ref, computed, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import HeaderBase from "@/components/HeaderBase.vue";
import BaseInput from "@/components/BaseInput.vue";
import axios from 'axios';
import type { ApiError } from "@/types.ts";

interface LocalRegisterForm {
    name: string;
    birth_date: string;
    phone: string;
    shipping_address: string;
    billing_address: string;
    same_address: boolean;
    document_id: string;
    referral_code: string;
    email: string;
    password: string;
    password_confirmation: string;
}

const auth = useAuthStore();
const router = useRouter();

const form = reactive<LocalRegisterForm>({
    name: '',
    birth_date: '',
    phone: '',
    shipping_address: '',
    billing_address: '',
    same_address: true,
    document_id: '',
    referral_code: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const errorMessage = ref<string | null>(null);
const loading = ref<boolean>(false);

const formatName = (): void => {
    form.name = form.name
        .replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')
        .replace(/\s+/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};

const passwordStrength = computed(() => {
    const p = form.password;
    if (!p) return { label: '', level: 0, color: 'bg-transparent', width: '0%' };

    let score = 0;
    if (p.length >= 8) score++;
    if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;

    if (score <= 1) return { label: 'Débil', level: 1, color: 'bg-error', width: '33%' };
    if (score === 2 || score === 3) return { label: 'Medio', level: 2, color: 'bg-yellow-500', width: '66%' };
    return { label: 'Fuerte', level: 3, color: 'bg-green-500', width: '100%' };
});

const isDobValid = computed((): boolean => {
    if (!form.birth_date) return false;
    const dob = new Date(form.birth_date);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    return age >= 18 && age < 100;
});

watch(() => form.same_address, (val) => {
    if (val) form.billing_address = form.shipping_address;
});

watch(() => form.shipping_address, (val) => {
    if (form.same_address) form.billing_address = val;
});

const validateForm = (): boolean => {
    formatName();
    const nameWords = form.name.trim().split(' ').length;
    if (nameWords < 2 || nameWords > 4) {
        errorMessage.value = 'Debe contener entre 1 y 2 nombres, y 1 y 2 apellidos.';
        return false;
    }

    if (!isDobValid.value) {
        errorMessage.value = 'Debes tener entre 18 y 99 años.';
        return false;
    }

    const phoneRegex = /^\+[1-9]\d{1,14}$/;
    if (!phoneRegex.test(form.phone)) {
        errorMessage.value = 'El teléfono debe incluir el código internacional (ej. +34600000000).';
        return false;
    }

    if (form.shipping_address.trim().length < 5) {
        errorMessage.value = 'La dirección de envío no es válida.';
        return false;
    }

    if (passwordStrength.value.level < 2) {
        errorMessage.value = 'La contraseña debe ser de nivel medio o fuerte.';
        return false;
    }

    if (form.password !== form.password_confirmation) {
        errorMessage.value = 'Las contraseñas no coinciden.';
        return false;
    }

    return true;
};

const handleSubmit = async (): Promise<void> => {
    errorMessage.value = null;

    if (!validateForm()) return;

    loading.value = true;

    try {
        const payload = { ...form };
        const { data } = await axios.post('/api/register', payload);

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
        <HeaderBase />

        <div class="flex-1 flex items-center justify-center p-6 mt-8 mb-8 text-slate-700">
            <div class="w-full max-w-2xl bg-slate-100 rounded shadow-2xl border  overflow-hidden">
                <div class="p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-slate-900">Crear cuenta</h1>
                        <p class="mt-2 text-sm">Regístrate para comenzar</p>
                    </div>

                    <form @submit.prevent="handleSubmit" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <BaseInput
                            v-model="form.name"
                            @input="formatName"
                            @blur="formatName"
                            label="Nombre y Apellidos"
                            icon="pi pi-user"
                            placeholder="Ej. Derek Murillo"
                            required
                            class="md:col-span-2"
                        />

                        <BaseInput
                            v-model="form.birth_date"
                            type="date"
                            label="Fecha de Nacimiento"
                            icon="pi pi-calendar"
                            required
                        />

                        <BaseInput
                            v-model="form.phone"
                            type="tel"
                            label="Teléfono"
                            icon="pi pi-phone"
                            placeholder="+34600000000"
                            required
                        />

                        <BaseInput
                            v-model="form.shipping_address"
                            label="Dirección de Envío"
                            icon="pi pi-map-marker"
                            placeholder="Calle principal 123, Ciudad, País"
                            required
                            class="md:col-span-2"
                        />

                        <div class="flex items-center gap-2 md:col-span-2 mt-1">
                            <input type="checkbox" id="sameAddress" v-model="form.same_address" class="w-4 h-4  bg-primary border-gray-300 rounded focus:ring-primary">
                            <label for="sameAddress" class="text-sm ">La dirección de facturación es la misma que la de envío</label>
                        </div>

                        <div v-if="!form.same_address" class="md:col-span-2 animate-fade-in">
                            <BaseInput
                                v-model="form.billing_address"
                                label="Dirección de Facturación"
                                icon="pi pi-receipt"
                                placeholder="Dirección fiscal"
                                required
                            />
                        </div>

                        <BaseInput
                            v-model="form.document_id"
                            label="DNI / NIE"
                            icon="pi pi-id-card"
                            placeholder="12345678A"
                            required
                        />

                        <BaseInput
                            v-model="form.referral_code"
                            label="Código de Referido (Opcional)"
                            icon="pi pi-tags"
                            placeholder="AMIGO2026"
                        />

                        <div class="md:col-span-2 border-t border-slate-200 pt-4 mt-2">
                            <BaseInput
                                v-model="form.email"
                                type="email"
                                label="Email"
                                icon="pi pi-envelope"
                                placeholder="tu@email.com"
                                required
                            />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <BaseInput
                                v-model="form.password"
                                type="password"
                                label="Contraseña"
                                icon="pi pi-lock"
                                placeholder="••••••••"
                                minlength="8"
                                required
                            />
                            <div v-if="form.password" class="mt-1">
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all duration-300" :class="passwordStrength.color" :style="{ width: passwordStrength.width }"></div>
                                </div>
                                <p class="text-xs mt-1 font-medium" :class="{'text-error': passwordStrength.level===1, 'text-yellow-600': passwordStrength.level===2, 'text-green-600': passwordStrength.level===3}">
                                    Seguridad: {{ passwordStrength.label }}
                                </p>
                            </div>
                        </div>

                        <BaseInput
                            v-model="form.password_confirmation"
                            type="password"
                            label="Confirmar Contraseña"
                            icon="pi pi-check-circle"
                            placeholder="••••••••"
                            required
                        />

                        <div v-if="errorMessage" class="md:col-span-2 flex items-center gap-2 p-3 mt-1 rounded bg-error/10 border border-error/20 text-error text-sm font-medium animate-fade-in">
                            <i class="pi pi-exclamation-circle text-base"></i>
                            <span>{{ errorMessage }}</span>
                        </div>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="md:col-span-2 w-full py-3 px-4 mt-3 bg-primary hover:bg-primary-hover active:bg-primary-active text-white font-semibold rounded transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        >
                            <i v-if="loading" class="pi pi-spinner pi-spin"></i>
                            <span>{{ loading ? 'Creando cuenta...' : 'Registrarse' }}</span>
                        </button>

                        <div class="md:col-span-2 text-center mt-2">
                            <router-link :to="{ name: 'login' }" class="text-sm  hover:-hover font-medium transition-colors">
                                ¿Ya tienes una cuenta? Inicia sesión
                            </router-link>
                        </div>
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
