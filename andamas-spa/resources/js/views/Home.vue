<template>
    <div class="space-y-6">
        <p class="text-gray-700">Selecciona un cliente para ver su resumen:</p>

        <!-- 🔍 Buscador de clientes -->
        <BuscadorCliente />

        <!-- 💰 Formulario de abono -->
        <FormularioAbono @abono-guardado="cargarResumen" />

        <!-- 📊 Tarjetas resumen -->
        <div
            v-if="cliente && resumen"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-6"
        >
            <ResumenCard
                titulo="Pedidos totales"
                :valor="resumen.totalProductos"
            />
            <ResumenCard
                titulo="Disponible en tienda"
                :valor="resumen.totalEnTienda"
            />
            <ResumenCard
                titulo="En camino"
                :valor="resumen.totalRecogidos"
            />
            <ResumenCard
                titulo="Total a pagar"
                :valor="formatearMoneda(resumen.totalAPagar)"
            />
            <ResumenCard
                titulo="Total abonado"
                :valor="formatearMoneda(resumen.totalAbonado)"
            />
            <ResumenCard
                titulo="Último abono"
                :valor="resumen.ultimoAbono || 'Sin abonos'"
            />
        </div>

        <div v-else class="text-gray-500 mt-6">
            No se ha seleccionado ningún cliente.
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";

// 🧠 Store del cliente seleccionado
import { useClienteStore } from "@/stores/clienteStore";

// 🧩 Componentes
import BuscadorCliente from "@/components/BuscarClientes.vue";
import ResumenCard from "@/components/ResumenCard.vue";
import FormularioAbono from "@/components/FormularioAbono.vue";

const resumen = ref(null);
const clienteStore = useClienteStore();
const cliente = ref(clienteStore.clienteActivo);

// 🔄 Función reutilizable para cargar resumen
const cargarResumen = async () => {
    if (!clienteStore.clienteActivo) return;

    try {
        const { data } = await axios.get(
            `/api/clientes/${clienteStore.clienteActivo.id}/resumen`
        );
        resumen.value = data;
    } catch (error) {
        console.error("Error cargando resumen del cliente:", error);
        resumen.value = null;
    }
};

// 👀 Watch para detectar cambio de cliente activo
watch(
    () => clienteStore.clienteActivo,
    async (nuevoCliente) => {
        cliente.value = nuevoCliente;

        if (!nuevoCliente) {
            resumen.value = null;
            return;
        }

        await cargarResumen();
    },
    { immediate: true }
);

// 💲 Formateador de moneda
const formatearMoneda = (valor) =>
    valor
        ? `$ ${Number(valor).toLocaleString("es-MX", {
              minimumFractionDigits: 2,
          })}`
        : "$ 0.00";
</script>
