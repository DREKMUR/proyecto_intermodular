<script setup lang="ts">
import HeaderBase from "@/components/HeaderBase.vue";
import AboutUsView from "@/views/AboutUsView.vue";
</script>

<template>
    <div class="bg-gradient-to-tl from-primary to-bg text-white min-h-screen">
        <HeaderBase />

        <div class="mx-40 my-20 text-lg">
            <AboutUsView />
        </div>
    </div>
</template>
