<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import AdminNav from "@/components/admin/AdminNav.vue";
import Skeleton from '@volt/Skeleton.vue';

const users = ref<any[]>([]);
const loading = ref(true);

const fetchUsers = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/admin/users');
        users.value = data.data;
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
};

const toggleAdmin = async (user: any) => {
    try {
        await axios.put(`/api/admin/users/${user.id}`, { is_admin: !user.is_admin });
        await fetchUsers();
    } catch (e) { console.error(e); }
};

const deleteUser = async (id: number) => {
    if (!confirm('¿Eliminar este usuario?')) return;
    try {
        await axios.delete(`/api/admin/users/${id}`);
        await fetchUsers();
    } catch (e) { console.error(e); }
};

onMounted(fetchUsers);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <AdminNav />

            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2"><i class="pi pi-users"></i> Usuarios</h2>

            <Skeleton v-if="loading" width="100%" height="300px" />

            <div v-else class="overflow-x-auto bg-black/30 rounded-2xl border border-white/5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-left">
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">ID</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Nombre</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Email</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Teléfono</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Admin</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Pedidos</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Registro</th>
                            <th class="p-3 font-semibold uppercase tracking-wider opacity-60 text-xs">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="u in users" :key="u.id" class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="p-3 font-mono">#{{ u.id }}</td>
                            <td class="p-3 font-semibold">{{ u.name }}</td>
                            <td class="p-3 opacity-70">{{ u.email }}</td>
                            <td class="p-3 text-xs">{{ u.phone || '—' }}</td>
                            <td class="p-3">
                                <button
                                    @click="toggleAdmin(u)"
                                    :class="u.is_admin ? 'bg-green-500/20 text-green-400' : 'bg-white/10 text-white/50'"
                                    class="px-3 py-1 rounded-lg text-xs font-semibold border-0 cursor-pointer transition-colors hover:opacity-80"
                                >{{ u.is_admin ? 'Sí' : 'No' }}</button>
                            </td>
                            <td class="p-3">{{ u.orders_count ?? 0 }}</td>
                            <td class="p-3 text-xs opacity-60">{{ new Date(u.created_at).toLocaleDateString() }}</td>
                            <td class="p-3">
                                <button
                                    class="px-3 py-1.5 rounded-lg text-sm font-semibold border-0 cursor-pointer transition-colors bg-red-500/20 text-red-400 hover:bg-red-500/30"
                                    @click="deleteUser(u.id)"
                                ><i class="pi pi-trash"></i></button>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="8" class="p-6 text-center opacity-50">No hay usuarios.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
        <FooterBase />
    </div>
</template>
