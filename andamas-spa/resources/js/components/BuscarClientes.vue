<template>
    <div>
        <input
            v-model="busqueda"
            @input="buscar"
            placeholder="Buscar cliente por nombre o telefono"
            class="border p-2 rounded w-full mb-4"
        />

        <ul
            v-if="clientes.length"
            class="bg-white border rounded shadow max-h-60 overflow-y-auto"
        >
            <li
                v-for="cliente in clientes"
                :key="cliente.id"
                @click="seleccionarCliente(cliente)"
                class="py-2 px-3 border-b hover:bg-blue-100 cursor-pointer"
            >
                {{ cliente.nombre }} – {{ cliente.telefono }}
            </li>
        </ul>

        <p v-if="!clientes.length && busqueda">No se encontraron resultados</p>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useClienteStore } from "@/stores/clienteStore";

const busqueda = ref("");
const clientes = ref([]);

const clienteStore = useClienteStore();

const buscar = async () => {
    if (busqueda.value.length < 2) {
        clientes.value = [];
        return;
    }

    try {
        const { data } = await axios.get("/api/clientes", {
            params: { search: busqueda.value },
        });
        clientes.value = data;
    } catch (error) {
        console.error("Error buscando clientes:", error);
    }
};

const seleccionarCliente = (cliente) => {
    clienteStore.seleccionarCliente(cliente);
    clientes.value = [];
    busqueda.value = "";
};
</script>
