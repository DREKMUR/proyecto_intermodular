<template>
    <div>
        <div class="flex items-center gap-4 mb-8 text-slate-100">
            <i class="pi pi-shield text-3xl"></i>
            <h1 class="text-3xl font-black uppercase tracking-wide">Panel Admin</h1>
        </div>

        <nav class="flex flex-wrap gap-2 mb-10 border-b border-white/10 pb-4">
            <router-link
                v-for="item in items"
                :key="item.to.name"
                :to="item.to"
                class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors"
                :class="$route.name === item.to.name ? 'bg-white/10 font-semibold' : 'hover:bg-white/5'"
            >
                <i :class="item.icon"></i>
                {{ item.label }}
            </router-link>
        </nav>
    </div>
</template>

<script setup lang="ts">
const items = [
    { to: { name: 'admin.dashboard' }, icon: 'pi pi-home',      label: 'Dashboard' },
    { to: { name: 'admin.products' },   icon: 'pi pi-box',       label: 'Productos' },
    { to: { name: 'admin.orders' },     icon: 'pi pi-shopping-bag', label: 'Pedidos' },
    { to: { name: 'admin.users' },      icon: 'pi pi-users',     label: 'Usuarios' },
    { to: { name: 'admin.tickets' },    icon: 'pi pi-ticket',    label: 'Tickets' },
    { to: { name: 'admin.sales' },      icon: 'pi pi-chart-bar', label: 'Ventas' },
];
</script>
