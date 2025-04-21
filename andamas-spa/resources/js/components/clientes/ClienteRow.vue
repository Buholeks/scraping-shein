<template>
    <tr class="hover:bg-gray-50 transition-colors duration-150">
        <td class="px-4 py-3 font-medium text-gray-800">
            {{ cliente.nombre }}
        </td>
        <td class="px-4 py-3 text-gray-600">
            {{ cliente.telefono }}
        </td>
        <td class="px-4 py-3 text-gray-600">
            {{ cliente.direccion }}
        </td>
        <td class="px-4 py-3 text-center">
            <div class="flex justify-center gap-2">
                <button
                    @click="$emit('editar', cliente)"
                    class="text-blue-600 hover:text-blue-800 transition font-medium flex items-center gap-1 text-sm"
                >
                    <fa icon="pen-to-square" />
                    Editar
                </button>
                <button
                    @click="$emit('eliminar', cliente.id)"
                    class="text-red-600 hover:text-red-800 transition font-medium flex items-center gap-1 text-sm"
                >
                    <fa icon="fa-trash-can " /> Eliminar
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup>
defineProps(["cliente"]);
defineEmits(["editar", "eliminar"]);
</script>
