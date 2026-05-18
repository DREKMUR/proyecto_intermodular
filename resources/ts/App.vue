<script setup lang="ts">
import { RouterView } from "vue-router";
import Toast from "@volt/Toast.vue";
</script>
<template>
    <Toast />
    <div class="flex flex-col bg-linear-to-tl from-black to-primary text-white min-h-screen">
        <RouterView />
    </div>
</template>
