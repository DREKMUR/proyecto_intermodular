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
