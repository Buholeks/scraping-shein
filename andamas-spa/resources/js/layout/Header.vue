<script setup>
import { ref } from 'vue'; 
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();
const cargandoSucursal = ref(false);

const cambiarSucursal = async (nuevoIdSucursal) => {
    try {
        cargandoSucursal.value = true;
        await authStore.cambiarSucursal(nuevoIdSucursal);
    } catch (error) {
        console.error("Error cambiando sucursal:", error);
    } finally {
        cargandoSucursal.value = false;
    }
};
</script>

<template>
  <header class="bg-white shadow-md py-4 px-6 border-b">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      
      <!-- Branding -->
      <div class="flex items-center gap-4">
        <h1 class="text-xl font-bold text-gray-800">📦 Mi App</h1>
        <div class="text-sm text-gray-500 border-l pl-4">
          <p><span class="font-semibold">Empresa:</span> {{ authStore.empresaInfo.nombre }}</p>
        </div>
      </div>

      <!-- Info usuario y sucursal -->
      <div class="flex items-center gap-6">
        
        <!-- Selector de sucursal -->
        <div class="text-sm text-gray-700">
          <label class="block text-xs text-gray-500 mb-1">Sucursal actual</label>
          <select
            :value="authStore.sucursalActual?.id"
            @change="cambiarSucursal(Number($event.target.value))"
            :disabled="cargandoSucursal"
            class="border rounded-md px-3 py-1 text-sm bg-white shadow-sm focus:ring focus:ring-blue-200 focus:outline-none"
          >
            <option
              v-for="sucursal in authStore.sucursales"
              :key="sucursal.id"
              :value="sucursal.id"
            >
              {{ sucursal.nombre }}
            </option>
          </select>
          <span v-if="cargandoSucursal" class="text-xs text-blue-600">Cambiando...</span>
        </div>

        <!-- Usuario -->
        <div class="flex items-center gap-2">
          <div
            class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm"
          >
            {{ authStore.userInitials }}
          </div>
          <div class="text-sm leading-tight">
            <p class="font-semibold text-gray-800">{{ authStore.user?.name }}</p>
            <p class="text-gray-500 text-xs">{{ authStore.user?.email }}</p>
          </div>
        </div>

        <!-- Botón logout -->
        <button
          @click="authStore.logout"
          class="text-sm text-red-600 hover:underline"
        >
          Cerrar sesión
        </button>
      </div>
    </div>
  </header>
</template>
