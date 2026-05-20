import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("@/views/HomePage.vue"),
            meta: { title: "Inicio" },
        },
        {
            path: "/cart",
            name: "cart",
            component: () => import("@/views/Cart.vue"),
            meta: { title: "Carrito" },
        },
        {
            path: "/catalog",
            name: "catalog",
            component: () => import("@/views/Catalog.vue"),
            meta: { title: "Catálogo" },
        },
        {
            path: '/cars/:slug',
            name: 'car.show',
            component: () => import("@/views/CarView.vue"),
            props: route => ({
                slug: route.params.slug,
                id: history.state.id
            }),
            meta: { title: "Vista de producto" },
        },
        {
            path: "/checkout",
            name: "checkout",
            component: () => import("@/views/CheckoutView.vue"),
            meta: { title: "Pasarela de pago" },
        },
        {
            path: "/contactUs",
            name: "contactUs",
            component: () => import("@/views/ContactUs.vue"),
            meta: { title: "Contáctanos" },
        },
        {
            path: "/whereWeAre",
            name: "whereWeAre",
            component: () => import("@/views/WhereWeAreView.vue"),
            meta: { title: "Donde encontrarnos" },
        },
        {
            path: "/FAQ",
            name: "FAQ",
            component: () => import("@/views/FAQView.vue"),
            meta: { title: "Preguntas freqüentes" },
        },
        {
            path: "/listTickets",
            name: "listTickets",
            component: () => import("@/views/ListTickets.vue"),
            meta: { title: "Gestion de Tickets", requiresAuth: true },
        },
        {
            path: "/profile",
            name: "profile",
            component: () => import("@/views/PerfilCliente.vue"),
            meta: { title: "Mi Perfil", requiresAuth: true },
        },
        {
            path: "/my-orders",
            name: "myOrders",
            component: () => import("@/views/HistorialPedidos.vue"),
            meta: { title: "Mis Pedidos", requiresAuth: true },
        },
        {
            path: "/login",
            name: "login",
            component: () => import("@/components/auth/Login.vue"),
            meta: { title: "Inicio sesión" },
        },
        {
            path: "/register",
            name: "register",
            component: () => import("@/components/auth/Register.vue"),
            meta: { title: "Registro" },
        },
        {
            path: "/admin",
            name: "admin.dashboard",
            component: () => import("@/views/admin/AdminDashboard.vue"),
            meta: { title: "Panel Admin", requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/products",
            name: "admin.products",
            component: () => import("@/views/admin/AdminProducts.vue"),
            meta: { title: "Productos - Admin", requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/orders",
            name: "admin.orders",
            component: () => import("@/views/admin/AdminOrders.vue"),
            meta: { title: "Pedidos - Admin", requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/users",
            name: "admin.users",
            component: () => import("@/views/admin/AdminUsers.vue"),
            meta: { title: "Usuarios - Admin", requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/tickets",
            name: "admin.tickets",
            component: () => import("@/views/admin/AdminTickets.vue"),
            meta: { title: "Tickets - Admin", requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/sales",
            name: "admin.sales",
            component: () => import("@/views/admin/AdminSalesChart.vue"),
            meta: { title: "Ventas - Admin", requiresAuth: true, requiresAdmin: true },
        },
    ],
});

router.beforeEach((to, _from, next) => {
    const auth = useAuthStore();
    const baseTitle = "DeMuFe";

    document.title = to.meta.title ? `${to.meta.title} - ${baseTitle}` : baseTitle;

    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return next('/login');
    }

    if (to.meta.requiresAdmin && !auth.isAdmin) {
        return next('/');
    }

    next();
});
export default router;
