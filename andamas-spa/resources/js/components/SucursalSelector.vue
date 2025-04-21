<script setup>
import { useAuthStore } from '@/stores/authStore';
const auth = useAuthStore();
</script>

<template>
  <select
    v-model="auth.sucursalActual.id"
    @change="auth.cambiarSucursal($event.target.value)"
    class="border p-1 rounded"
  >
    <option disabled value="">Selecciona una sucursal</option>
    <option v-for="sucursal in auth.sucursales" :key="sucursal.id" :value="sucursal.id">
      {{ sucursal.nombre }}
    </option>
  </select>
</template>
