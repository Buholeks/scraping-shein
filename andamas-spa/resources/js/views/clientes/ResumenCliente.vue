<template>
    <div class="space-y-4">
      <h2 class="text-xl font-bold">Resumen del Cliente</h2>
  
      <div class="grid grid-cols-2 gap-4">
        <div>Total de productos: {{ resumen.totalProductos }}</div>
        <div>En tienda: {{ resumen.totalEnTienda }}</div>
        <div>Recogidos: {{ resumen.totalRecogidos }}</div>
        <div>Total a pagar: ${{ resumen.totalAPagar }}</div>
        <div>Total abonado: ${{ resumen.totalAbonado }}</div>
        <div>Último abono: {{ resumen.ultimoAbono || 'Sin abonos' }}</div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  const resumen = ref({});
  
  const clienteId = 1; // puedes recibirlo por prop o route
  
  const cargarResumen = async () => {
    const { data } = await axios.get(`/api/clientes/${clienteId}/resumen`);
    resumen.value = data;
  };
  
  onMounted(cargarResumen);
  </script>
  