<script setup lang="ts">
import HeaderBase from "@/components/HeaderBase.vue";
import AboutUsView from "@/views/AboutUsView.vue";
import FooterBase from "@/components/FooterBase.vue";
</script>

<template>
    <HeaderBase />

    <div class="mx-6 sm:mx-16 lg:mx-40 my-10 md:my-20 text-lg">
        <AboutUsView />
    </div>

    <FooterBase />
</template>
