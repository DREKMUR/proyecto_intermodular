import { createRouter, createWebHistory } from "vue-router";

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
            component: () => import("@/components/Cart.vue"),
            meta: { title: "Carrito" },
        },
    ],
});

router.beforeEach((to, from) => {
    const baseTitle = "DeMuFe";
    const pageTitle = to.meta.title as string;
    document.title = pageTitle ? `${pageTitle} - ${baseTitle}` : baseTitle;
});

export default router;
