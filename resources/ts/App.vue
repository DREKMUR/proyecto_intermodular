<script setup lang="ts">
import { RouterView } from "vue-router";
import { ref, onMounted, watch } from "vue";
import Toast from "@volt/Toast.vue";
import { useAuthStore } from "@/stores/auth.ts";
import ReviewReminderModal from "@/components/ReviewReminderModal.vue";

const authStore = useAuthStore();
const reviewReminder = ref<InstanceType<typeof ReviewReminderModal> | null>(null);

onMounted(() => {
    if (authStore.isLoggedIn) {
        reviewReminder.value?.checkPending();
    }
    window.addEventListener('check-pending-opinion', () => {
        reviewReminder.value?.checkPending();
    });
});

watch(() => authStore.isLoggedIn, (loggedIn) => {
    if (loggedIn) {
        setTimeout(() => reviewReminder.value?.checkPending(), 500);
    }
});
</script>
<template>
    <Toast />
    <div class="flex flex-col bg-linear-to-tl from-black to-primary text-white min-h-screen">
        <RouterView />
    </div>
    <ReviewReminderModal ref="reviewReminder" />
</template>
