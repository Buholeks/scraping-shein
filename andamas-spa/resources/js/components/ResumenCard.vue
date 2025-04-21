<template>
    <div class="bg-white shadow rounded-xl p-5 border hover:shadow-md transition">
      <h3 class="text-sm font-semibold text-gray-600 mb-1">{{ titulo }}</h3>
      <p class="text-2xl font-bold text-blue-600">{{ valor }}</p>
    </div>
  </template>
  
  <script setup>
  defineProps({
    titulo: String,
    valor: [String, Number]
  })
  </script>
  