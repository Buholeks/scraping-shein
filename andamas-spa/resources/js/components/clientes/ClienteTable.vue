<template>
    <div
        class="overflow-x-auto bg-white rounded-lg shadow ring-1 ring-gray-200"
    >
        <table
            class="min-w-full divide-y divide-gray-200 text-sm text-gray-700"
        >
            <thead
                class="bg-gray-300 text-gray-700 uppercase tracking-wider text-xs"
            >
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Teléfono</th>
                    <th class="px-4 py-3 text-left">Dirección</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <ClienteRow
                    v-for="cliente in clientes"
                    :key="cliente.id"
                    :cliente="cliente"
                    @editar="emit('editar', $event)"
                    @eliminar="emit('eliminar', $event)"
                />
            </tbody>
        </table>
    </div>
</template>

<script setup>
import ClienteRow from "./ClienteRow.vue";
defineProps(["clientes"]);
const emit = defineEmits(["editar", "eliminar"]);
</script>
