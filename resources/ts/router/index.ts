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
    ],
});

router.beforeEach((to, from, next) => {
    const baseTitle = "Proyecto intermodular";
    const pageTitle = to.meta.title as string;
    document.title = pageTitle ? `${pageTitle} - ${baseTitle}` : baseTitle;
    next();
});

export default router;
