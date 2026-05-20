<script setup lang="ts">
import { reactive, ref, computed, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import HeaderBase from "@/components/HeaderBase.vue";
import BaseInput from "@/components/BaseInput.vue";
import axios from 'axios';
import type { ApiError } from "@/types.ts";

interface FieldState {
    touched: boolean;
    valid: boolean;
}

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

const fieldState = reactive<Record<string, FieldState>>({
    name: { touched: false, valid: false },
    birth_date: { touched: false, valid: false },
    phone: { touched: false, valid: false },
    shipping_address: { touched: false, valid: false },
    billing_address: { touched: false, valid: false },
    document_id: { touched: false, valid: false },
    email: { touched: false, valid: false },
    password: { touched: false, valid: false },
    password_confirmation: { touched: false, valid: false },
});

const formatName = (): void => {
    form.name = form.name
        .replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')
        .replace(/\s+/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};

const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ]+){1,3}$/;
const phoneRegex = /^\+[1-9]\d{1,14}$/;
const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
const addressRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+\s+\d+.*,\s*\d{5}\s+[\w\sáéíóúÁÉÍÓÚñÑ]+$/;

function parseDDMMYYYY(value: string): { valid: boolean; date: Date | null; age: number } {
    const m = value.match(dateRegex);
    if (!m) return { valid: false, date: null, age: -1 };
    const day = parseInt(m[1], 10);
    const month = parseInt(m[2], 10) - 1;
    const year = parseInt(m[3], 10);
    if (month < 0 || month > 11 || day < 1 || day > 31) return { valid: false, date: null, age: -1 };
    const date = new Date(year, month, day);
    if (date.getDate() !== day || date.getMonth() !== month || date.getFullYear() !== year)
        return { valid: false, date: null, age: -1 };
    const today = new Date();
    let age = today.getFullYear() - year;
    const mDiff = today.getMonth() - month;
    if (mDiff < 0 || (mDiff === 0 && today.getDate() < day)) age--;
    return { valid: true, date, age };
}

function validateName(): boolean {
    return nameRegex.test(form.name.trim());
}
function validateDob(): boolean {
    const result = parseDDMMYYYY(form.birth_date);
    return result.valid && result.age >= 18 && result.age < 100;
}
function validatePhone(): boolean {
    return phoneRegex.test(form.phone);
}
function validateShippingAddress(): boolean {
    return addressRegex.test(form.shipping_address.trim());
}
function validateBillingAddress(): boolean {
    if (form.same_address) return true;
    return addressRegex.test(form.billing_address.trim());
}
function validateDocumentId(): boolean {
    return form.document_id.trim().length >= 5;
}
function validateEmail(): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email);
}
function validatePassword(): boolean {
    return passwordStrength.value.level >= 2;
}
function validatePasswordConfirmation(): boolean {
    return form.password === form.password_confirmation && form.password.length > 0;
}

const validators: Record<string, () => boolean> = {
    name: validateName,
    birth_date: validateDob,
    phone: validatePhone,
    shipping_address: validateShippingAddress,
    billing_address: validateBillingAddress,
    document_id: validateDocumentId,
    email: validateEmail,
    password: validatePassword,
    password_confirmation: validatePasswordConfirmation,
};

function touchAndValidate(field: string) {
    fieldState[field].touched = true;
    fieldState[field].valid = validators[field]();
}

function onChange(field: string) {
    if (fieldState[field].touched) {
        fieldState[field].valid = validators[field]();
    }
}

const passwordStrength = computed(() => {
    const p = form.password;
    if (!p) return { label: '', level: 0 };

    let score = 0;
    if (p.length >= 8) score++;
    if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;

    if (score <= 1) return { label: 'Débil', level: 1 };
    if (score === 2 || score === 3) return { label: 'Medio', level: 2 };
    return { label: 'Fuerte', level: 3 };
});

watch(() => form.same_address, (val) => {
    if (val) form.billing_address = form.shipping_address;
});

watch(() => form.shipping_address, (val) => {
    if (form.same_address) form.billing_address = val;
});

watch(() => form.birth_date, () => onChange('birth_date'));

const validateForm = (): boolean => {
    formatName();
    const fields = ['name', 'birth_date', 'phone', 'shipping_address', 'document_id', 'email', 'password', 'password_confirmation'];
    for (const f of fields) {
        touchAndValidate(f);
        if (!fieldState[f].valid) {
            errorMessage.value = 'Revisa los campos marcados en rojo.';
            return false;
        }
    }
    return true;
};

const handleSubmit = async (): Promise<void> => {
    errorMessage.value = null;
    if (!validateForm()) return;

    loading.value = true;

    const dobResult = parseDDMMYYYY(form.birth_date);
    const formattedDate = dobResult.date
        ? `${dobResult.date.getFullYear()}-${String(dobResult.date.getMonth() + 1).padStart(2, '0')}-${String(dobResult.date.getDate()).padStart(2, '0')}`
        : '';

    try {
        const payload = {
            ...form,
            birth_date: formattedDate,
        };
        const { data } = await axios.post('/api/register', payload);

        auth.token = data.access_token;
        auth.user = data.user;

        localStorage.setItem('auth_token', data.access_token);
        localStorage.setItem('user', JSON.stringify(data.user));

        axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;

        if (auth.isAdmin) {
            await router.push({ name: 'admin.dashboard' });
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

const validationProp = (field: string): 'default' | 'valid' | 'invalid' => {
    if (!fieldState[field].touched) return 'default';
    return fieldState[field].valid ? 'valid' : 'invalid';
};
</script>

<template>
        <HeaderBase />

        <div class="flex-1 flex items-center justify-center p-6 mt-8 mb-8 text-slate-700">
            <div class="w-full max-w-2xl bg-slate-100 rounded shadow-2xl border overflow-hidden">
                <div class="p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-slate-900">Crear cuenta</h1>
                        <p class="mt-2 text-sm">Regístrate para comenzar</p>
                    </div>

                    <form @submit.prevent="handleSubmit" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <BaseInput
                            v-model="form.name"
                            @input="formatName"
                            @blur="touchAndValidate('name')"
                            label="Nombre y Apellidos"
                            icon="pi pi-user"
                            placeholder="Ej. Derek Murillo"
                            required
                            :validation="validationProp('name')"
                            class="md:col-span-2"
                        />

                        <BaseInput
                            v-model="form.birth_date"
                            type="text"
                            label="Fecha de Nacimiento"
                            icon="pi pi-calendar"
                            placeholder="DD/MM/YYYY"
                            required
                            :validation="validationProp('birth_date')"
                        />

                        <BaseInput
                            v-model="form.phone"
                            type="tel"
                            label="Teléfono"
                            icon="pi pi-phone"
                            placeholder="+34600000000"
                            required
                            :validation="validationProp('phone')"
                        />

                        <BaseInput
                            v-model="form.shipping_address"
                            @blur="touchAndValidate('shipping_address')"
                            label="Dirección de Envío"
                            icon="pi pi-map-marker"
                            placeholder="Calle principal 123, 28001 Madrid"
                            required
                            :validation="validationProp('shipping_address')"
                            class="md:col-span-2"
                        />

                        <div class="flex items-center gap-2 md:col-span-2 mt-1">
                            <input type="checkbox" id="sameAddress" v-model="form.same_address" class="w-4 h-4 bg-primary border-gray-300 rounded focus:ring-primary">
                            <label for="sameAddress" class="text-sm">La dirección de facturación es la misma que la de envío</label>
                        </div>

                        <div v-if="!form.same_address" class="md:col-span-2 animate-fade-in">
                            <BaseInput
                                v-model="form.billing_address"
                                @blur="touchAndValidate('billing_address')"
                                label="Dirección de Facturación"
                                icon="pi pi-receipt"
                                placeholder="Calle principal 123, 28001 Madrid"
                                required
                                :validation="validationProp('billing_address')"
                            />
                        </div>

                        <BaseInput
                            v-model="form.document_id"
                            @blur="touchAndValidate('document_id')"
                            label="DNI / NIE"
                            icon="pi pi-id-card"
                            placeholder="12345678A"
                            required
                            :validation="validationProp('document_id')"
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
                                @blur="touchAndValidate('email')"
                                label="Email"
                                icon="pi pi-envelope"
                                placeholder="tu@email.com"
                                required
                                :validation="validationProp('email')"
                            />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <BaseInput
                                v-model="form.password"
                                type="password"
                                @blur="touchAndValidate('password')"
                                label="Contraseña"
                                icon="pi pi-lock"
                                placeholder="••••••••"
                                minlength="8"
                                required
                                :validation="validationProp('password')"
                            />
                            <div v-if="form.password" class="mt-1">
                                <meter
                                    :value="passwordStrength.level"
                                    min="0"
                                    max="3"
                                    low="1"
                                    high="2"
                                    optimum="3"
                                    class="w-full h-2 rounded-full"
                                />
                                <p class="text-xs mt-1 font-medium"
                                    :class="{
                                        'text-red-600': passwordStrength.level === 1,
                                        'text-yellow-600': passwordStrength.level === 2,
                                        'text-green-600': passwordStrength.level === 3
                                    }"
                                >
                                    Seguridad: {{ passwordStrength.label }}
                                </p>
                            </div>
                        </div>

                        <BaseInput
                            v-model="form.password_confirmation"
                            type="password"
                            @blur="touchAndValidate('password_confirmation')"
                            label="Confirmar Contraseña"
                            icon="pi pi-check-circle"
                            placeholder="••••••••"
                            required
                            :validation="validationProp('password_confirmation')"
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
                            <router-link :to="{ name: 'login' }" class="text-sm hover:text-primary font-medium transition-colors">
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

meter {
    --meter-color-low: #dc2626;
    --meter-color-suboptimum: #ca8a04;
    --meter-color-optimum: #16a34a;
    background: #e5e7eb;
    border: none;
    border-radius: 999px;
}
meter::-webkit-meter-bar {
    background: #e5e7eb;
    border: none;
    border-radius: 999px;
}
meter::-webkit-meter-even-less-good-value {
    background: var(--meter-color-low);
    border-radius: 999px;
}
meter::-webkit-meter-suboptimum-value {
    background: var(--meter-color-suboptimum);
    border-radius: 999px;
}
meter::-webkit-meter-optimum-value {
    background: var(--meter-color-optimum);
    border-radius: 999px;
}
meter::-moz-meter-bar {
    border-radius: 999px;
}
meter:-moz-meter-optimum::-moz-meter-bar {
    background: var(--meter-color-optimum);
}
meter:-moz-meter-sub-optimum::-moz-meter-bar {
    background: var(--meter-color-suboptimum);
}
meter:-moz-meter-sub-sub-optimum::-moz-meter-bar {
    background: var(--meter-color-low);
}
</style>
