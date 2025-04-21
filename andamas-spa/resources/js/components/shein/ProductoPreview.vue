<template>
    <div class="bg-white p-4 rounded shadow space-y-2">
      <h3 class="text-xl font-semibold">Producto encontrado:</h3>
      <p><strong>SKU:</strong> {{ producto.sku }}</p>
      <p><strong>Título:</strong> {{ producto.title }}</p>
      <p><strong>Precio actual:</strong> {{ producto.price ?? 'No disponible' }}</p>
      <p v-if="producto.price_original">
        <strong>Precio original:</strong> <del>{{ producto.price_original }}</del>
      </p>
  
      <div class="my-3 flex gap-2 flex-wrap">
        <img
          v-for="img in producto.images"
          :key="img"
          :src="img"
          alt="img"
          class="h-24 rounded shadow"
        />
      </div>
  
      <button
        @click="$emit('agregar')"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        Agregar producto
      </button>
    </div>
  </template>
  
  <script setup>
  defineProps(['producto'])
  defineEmits(['agregar'])
  </script>
  