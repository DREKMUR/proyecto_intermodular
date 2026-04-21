<script setup lang="ts">
import HeaderBase from "@/components/HeaderBase.vue";
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth.ts";
import axios from "axios";
import type {DoubtsFormType} from "@/types.ts";

const authStore = useAuthStore();

const form = ref<DoubtsFormType>({
    name: authStore.user?.name ?? "",
    email: authStore.user?.email ?? "",
    productRef: "",
    description: "",
});

const descriptionMaxLength: number = 150;
const isSubmitting = ref<boolean>(false);
const submitted = ref<boolean>(false);
const showErrors = ref<boolean>(false);

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const isEmailValid = computed<boolean>(() =>
    authStore.isLoggedIn ? true : emailRegex.test(form.value.email)
);

const isNameValid = computed<boolean>(() =>
    authStore.isLoggedIn ? true : form.value.name.trim().length >= 2
);

const isProductRefValid = computed<boolean>(
    () => form.value.productRef.trim().length > 0
);

const isDescriptionValid = computed<boolean>(
    () =>
        form.value.description.trim().length > 0 &&
        form.value.description.length <= descriptionMaxLength
);

const isFormValid = computed<boolean>(
    () =>
        isNameValid.value &&
        isEmailValid.value &&
        isProductRefValid.value &&
        isDescriptionValid.value
);

async function handleSubmit(): Promise<boolean> {
    if (!isFormValid.value) {
        showErrors.value = true;
        return false;
    }

    showErrors.value = false;
    isSubmitting.value = true;

    if(authStore.isLoggedIn)
        form.value.id = authStore.user?.id;

    try{
        const res = await axios.post('/api/ticket', form.value);

        if(!res){
            return false;
        }

        submitted.value = true;
    }catch(error){
        console.error(error);
    }
    isSubmitting.value = false;
    return true;
}

function resetForm() {
    form.value = { name: "", email: "", productRef: "", description: "" };
    submitted.value = false;
    showErrors.value = false;
}
</script>

<template>
    <HeaderBase />

    <div class="flex flex-col gap-10 text-center mx-40 my-20 text-lg">
        <h1 class="text-4xl font-bold">¡Contáctanos!</h1>
        <p>
            Si tienes cualquier duda o problema, no olvides que puedes contactar con nosotros
            mediante el siguiente formulario.
        </p>

        <div
            v-if="!submitted"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-10 text-left text-black"
        >
            <div v-if="showErrors && !isFormValid" class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                Por favor, revisa los campos marcados en rojo antes de enviar.
            </div>

            <div
                v-if="authStore.isLoggedIn"
                class="flex items-center gap-3 mb-8 p-4 bg-slate-50 rounded-xl border border-slate-200"
            >
                <div
                    class="w-10 h-10 rounded-full bg-slate-800 text-white flex items-center justify-center font-semibold text-sm shrink-0"
                >
                    {{ authStore.user?.name.charAt(0) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-base leading-tight">
                        {{ authStore.user?.name }}
                    </p>
                    <p class="text-slate-500 text-sm">{{ authStore.user?.email }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div v-if="!authStore.isLoggedIn" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-700 tracking-wide">
                            Nombre y apellidos <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Derek Murillo Fernández"
                            class="w-full px-4 py-3 rounded-xl border text-base transition-all duration-200 outline-none"
                            :class="
                form.name.length > 0 || showErrors
                  ? isNameValid
                    ? 'border-emerald-400 bg-emerald-50/30 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100'
                    : 'border-rose-400 bg-rose-50/30 focus:border-rose-500 focus:ring-2 focus:ring-rose-100'
                  : 'border-slate-200 bg-slate-50/50 focus:border-slate-400 focus:ring-2 focus:ring-slate-100'
              "
                        />
                        <p
                            v-if="(form.name.length > 0 || showErrors) && !isNameValid"
                            class="text-xs text-rose-500"
                        >
                            Mínimo 2 caracteres.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-700 tracking-wide">
                            Correo electrónico <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="derek@example.com"
                            class="w-full px-4 py-3 rounded-xl border text-base transition-all duration-200 outline-none"
                            :class="
                form.email.length > 0 || showErrors
                  ? isEmailValid
                    ? 'border-emerald-400 bg-emerald-50/30 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100'
                    : 'border-rose-400 bg-rose-50/30 focus:border-rose-500 focus:ring-2 focus:ring-rose-100'
                  : 'border-slate-200 bg-slate-50/50 focus:border-slate-400 focus:ring-2 focus:ring-slate-100'
              "
                        />
                        <p
                            v-if="(form.email.length > 0 || showErrors) && !isEmailValid"
                            class="text-xs text-rose-500"
                        >
                            Introduce un correo válido.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700 tracking-wide">
                        Producto <span class="text-rose-500">*</span>
                    </label>
                    <input
                        v-model="form.productRef"
                        type="text"
                        placeholder="Nombre, referencia o código del producto"
                        class="w-full px-4 py-3 rounded-xl border text-base transition-all duration-200 outline-none"
                        :class="
              form.productRef.length > 0 || showErrors
                ? isProductRefValid
                  ? 'border-emerald-400 bg-emerald-50/30 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100'
                  : 'border-rose-400 bg-rose-50/30 focus:border-rose-500 focus:ring-2 focus:ring-rose-100'
                : 'border-slate-200 bg-slate-50/50 focus:border-slate-400 focus:ring-2 focus:ring-slate-100'
            "
                    />
                    <p class="text-xs text-slate-400">
                        Introduce el nombre, referencia o cualquier indicador visible del producto.
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-baseline">
                        <label class="text-sm font-semibold text-slate-700 tracking-wide">
                            Consulta <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        :maxlength="descriptionMaxLength"
                        placeholder="Escribe aquí tu consulta sobre el producto..."
                        class="w-full px-4 py-3 rounded-xl border text-base transition-all duration-200 outline-none resize-none"
                        :class="
              form.description.length > 0 || showErrors
                ? isDescriptionValid
                  ? 'border-emerald-400 bg-emerald-50/30 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100'
                  : 'border-rose-400 bg-rose-50/30 focus:border-rose-500 focus:ring-2 focus:ring-rose-100'
                : 'border-slate-200 bg-slate-50/50 focus:border-slate-400 focus:ring-2 focus:ring-slate-100'
            "
                    />
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        @click="handleSubmit"
                        :disabled="isSubmitting"
                        class="flex items-center gap-3 px-8 py-3.5 bg-primary text-white font-semibold rounded-xl text-base hover:bg-primary-hover transition-all duration-200 hover:cursor-pointer disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                    >
                        <svg
                            v-if="isSubmitting"
                            class="animate-spin h-5 w-5 text-white shrink-0"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ isSubmitting ? "Enviando..." : "Enviar consulta" }}
                    </button>
                </div>
            </div>
        </div>

        <Transition
            enter-active-class="transition-all duration-500 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
        >
            <div
                v-if="submitted"
                class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-12 flex flex-col items-center gap-6"
            >
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex flex-col gap-2 text-center">
                    <h2 class="text-2xl font-bold text-slate-800">¡Consulta enviada!</h2>
                    <p class="text-slate-500">
                        Hemos recibido tu consulta sobre
                        <span class="font-semibold text-slate-700">{{ form.productRef || "el producto" }}</span>.
                        Te responderemos lo antes posible.
                    </p>
                </div>
                <button
                    @click="resetForm"
                    class="text-sm text-slate-500 underline underline-offset-4 hover:text-slate-700 transition-colors"
                >
                    Hacer una nueva consulta
                </button>
            </div>
        </Transition>
    </div>
</template>
