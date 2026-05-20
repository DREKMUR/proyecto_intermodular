<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import InputText from '@volt/InputText.vue';
import { useToast } from 'primevue/usetoast';

interface ProfileForm {
    name: string;
    email: string;
    phone: string;
    shipping_address: string;
    billing_address: string;
    document_id: string;
}

const authStore = useAuthStore();
const toast = useToast();
const saving = ref(false);
const form = ref<ProfileForm>({
    name: '',
    email: '',
    phone: '',
    shipping_address: '',
    billing_address: '',
    document_id: '',
});

onMounted(() => {
    if (authStore.user) {
        form.value.name = authStore.user.name;
        form.value.email = authStore.user.email;
        form.value.phone = String(authStore.user.phone ?? '');
        form.value.shipping_address = authStore.user.shipping_address;
        form.value.billing_address = authStore.user.billing_address;
        form.value.document_id = authStore.user.document_id;
    }
});

async function saveProfile() {
    saving.value = true;
    try {
        const res = await axios.put('/api/profile', form.value);
        authStore.user = res.data.user;
        toast.add({ severity: 'success', summary: 'Perfil actualizado', life: 3000 });
    } catch (err: any) {
        const msg = err?.response?.data?.message || 'Error al guardar el perfil.';
        toast.add({ severity: 'error', summary: 'Error', detail: msg, life: 4000 });
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <HeaderBase />
    <main class="flex-1 flex justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <h1 class="text-3xl font-bold mb-8 text-center">Mi Perfil</h1>

            <div class="bg-white/5 rounded-xl p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-1">Nombre</label>
                    <InputText v-model="form.name" class="w-full" />
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Email</label>
                    <InputText v-model="form.email" class="w-full" />
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Teléfono</label>
                    <InputText v-model="form.phone" class="w-full" />
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Dirección de envío</label>
                    <InputText v-model="form.shipping_address" class="w-full" />
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Dirección de facturación</label>
                    <InputText v-model="form.billing_address" class="w-full" />
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Documento de identidad</label>
                    <InputText v-model="form.document_id" class="w-full" />
                </div>

                <button
                    @click="saveProfile"
                    :disabled="saving"
                    class="w-full bg-[#0061ad] hover:bg-[#005094] text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                >
                    {{ saving ? 'Guardando...' : 'Guardar cambios' }}
                </button>
            </div>
        </div>
    </main>
    <FooterBase />
</template>
