<script setup>
import { useAuthStore } from '@/stores/authStore';
import { onMounted } from 'vue';

const authStore = useAuthStore();

onMounted(() => {
  authStore.initialize(); // Cargar estado al iniciar
});
</script>

<template>
  <router-view />
</template>