<script setup lang="ts">
</script>

<template>
    <div class="z-1 flex px-10 items-center justify-between py-6 gap-20 bg-gradient-to-l from-secondary/100 to-black text-white sticky top-0 w-full shadow-xl">
        <RouterLink :to="'/'" class="w-2/12">
            <img src="/public/images/logo.png" alt="Logo" class="w-40 hover:scale-110 transition-transform" />
        </RouterLink>

        <div class="flex items-center 10/12">
            <RouterLink :to="'cart'">
                <a class="pi pi-shopping-cart  hover:scale-170 scale-150 transition-transform" />
            </RouterLink>
        </div>
    </div>
</template>
