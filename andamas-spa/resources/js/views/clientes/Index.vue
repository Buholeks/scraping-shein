<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">👥 Clientes</h2>

    <!-- Formulario de cliente -->
    <form @submit.prevent="guardarCliente" class="space-y-2 mb-6">
      <div class="flex gap-2 flex-wrap">
        <input v-model="form.nombre" placeholder="Nombre" class="border p-2 rounded w-full md:w-auto" />
        <input v-model="form.telefono" placeholder="Teléfono" class="border p-2 rounded w-full md:w-auto" />
        <input v-model="form.direccion" placeholder="Dirección" class="border p-2 rounded w-full md:w-auto" />

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
          {{ form.id ? 'Actualizar' : 'Agregar' }}
        </button>
        <button v-if="form.id" @click="cancelarEdicion" class="text-sm text-gray-500">Cancelar</button>
      </div>
    </form>

    <!-- Tabla de clientes -->
    <ClienteTable
      :clientes="clientes"
      @editar="editarCliente"
      @eliminar="eliminarCliente"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/authStore'
import ClienteTable from '../../components/clientes/ClienteTable.vue'

// Store de autenticación
const auth = useAuthStore()

// Estados reactivos
const clientes = ref([])
const form = ref({
  id: null,
  nombre: '',
  telefono: '',
  direccion: ''
})

// Cargar clientes al iniciar
const cargarClientes = async () => {
  const res = await axios.get('/api/clientes')
  clientes.value = res.data
}

// Guardar o actualizar cliente
const guardarCliente = async () => {
  if (!auth.sucursalActual?.id) {
    alert('Debes seleccionar una sucursal activa antes de guardar un cliente.')
    return
  }

  const payload = {
    ...form.value,
    id_sucursal: auth.sucursalActual.id // 👈 Se agrega aquí
  }

  if (form.value.id) {
    await axios.put(`/api/clientes/${form.value.id}`, payload)
  } else {
    await axios.post('/api/clientes', payload)
  }

  cancelarEdicion()
  cargarClientes()
}

// Editar cliente existente
const editarCliente = (cliente) => {
  form.value = { ...cliente }
}

// Eliminar cliente
const eliminarCliente = async (id) => {
  if (confirm('¿Eliminar este cliente?')) {
    await axios.delete(`/api/clientes/${id}`)
    cargarClientes()
  }
}

// Cancelar edición y limpiar formulario
const cancelarEdicion = () => {
  form.value = {
    id: null,
    nombre: '',
    telefono: '',
    direccion: ''
  }
}

onMounted(cargarClientes)
</script>
