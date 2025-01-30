import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("./Pages/Home.vue"),
    },
    {
        path: "/movie/:id",
        component: () => import("./Pages/Movie.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),

    routes
});