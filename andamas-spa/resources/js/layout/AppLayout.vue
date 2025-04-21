<template>
  <div class="flex h-screen">
      <!-- Sidebar fijo a la izquierda -->
      <Sidebar />

      <!-- Contenedor principal -->
      <div class="flex-1 flex flex-col">
          <!-- Header arriba -->
          <Header />

          <!-- Contenido dinámico -->
          <main class="flex-1 bg-gray-100 p-3 overflow-y-auto">
              <router-view />
          </main>
      </div>
  </div>
</template>

<script setup>
import Sidebar from "./Sidebar.vue";
import Header from "./Header.vue";

const getUser = async () => {
  try {
      const res = await api.get("/api/user");
      console.log(res.data); // Usuario logueado
  } catch (e) {
      console.log("No autenticado");
  }
};
</script>
