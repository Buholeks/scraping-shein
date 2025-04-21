// src/stores/clienteStore.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useClienteStore = defineStore('clienteStore', () => {
  const clienteActivo = ref(null)

  const seleccionarCliente = (cliente) => {
    clienteActivo.value = cliente
  }

  return { clienteActivo, seleccionarCliente }
})
