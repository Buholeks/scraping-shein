<template>
  <div :class="[
    'p-3 rounded-lg border flex items-start gap-3',
    tipo === 'exito' ? 'bg-green-50 border-green-200 text-green-700' : '',
    tipo === 'error' ? 'bg-red-50 border-red-200 text-red-700' : '',
    tipo === 'info' ? 'bg-blue-50 border-blue-200 text-blue-700' : ''
  ]">
    <fa 
      :icon="tipo === 'exito' ? 'check-circle' : tipo === 'error' ? 'exclamation-circle' : 'info-circle'" 
      class="mt-0.5 flex-shrink-0"
    />
    <div class="flex-1">
      <slot />
    </div>
    <button @click="$emit('cerrar')" class="text-gray-400 hover:text-gray-600">
      <fa icon="times" />
    </button>
  </div>
</template>

<script setup>
defineProps({
  tipo: {
    type: String,
    default: 'info',
    validator: (value) => ['exito', 'error', 'info'].includes(value)
  }
});

defineEmits(['cerrar']);
</script>