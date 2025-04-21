<template>
    <div>
      <h3 class="text-lg font-semibold">Abonos</h3>
      <form @submit.prevent="registrarAbono">
        <input v-model="nuevoAbono.monto" type="number" placeholder="Monto" class="input" />
        <input v-model="nuevoAbono.fecha_abono" type="date" class="input" />
        <button type="submit" class="btn">Registrar</button>
      </form>
  
      <ul class="mt-4">
        <li v-for="abono in abonos" :key="abono.id">
          {{ abono.fecha_abono }} - ${{ abono.monto }}
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  const abonos = ref([]);
  const nuevoAbono = ref({ monto: '', fecha_abono: '' });
  
  const clienteId = 1;
  
  const cargarAbonos = async () => {
    const { data } = await axios.get(`/api/clientes/${clienteId}/abonos`);
    abonos.value = data;
  };
  
  const registrarAbono = async () => {
    await axios.post(`/api/clientes/${clienteId}/abonos`, nuevoAbono.value);
    nuevoAbono.value = { monto: '', fecha_abono: '' };
    cargarAbonos();
  };
  
  onMounted(cargarAbonos);
  </script>
  