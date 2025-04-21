<template>
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">💰 Registrar Abono</h3>
        <form
            @submit.prevent="guardarAbono"
            class="flex flex-col sm:flex-row gap-3"
        >
            <input
                v-model="abono.monto"
                type="number"
                step="0.01"
                placeholder="Monto"
                class="border p-2 rounded w-full sm:w-40"
            />
            <input
                v-model="abono.fecha_abono"
                type="date"
                class="border p-2 rounded w-full sm:w-48"
            />
            <button
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
            >
                Guardar
            </button>
        </form>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { useClienteStore } from "@/stores/clienteStore";

const abono = ref({
    monto: "",
    fecha_abono: new Date().toISOString().slice(0, 10),
});

const clienteStore = useClienteStore();

// 📌 Define función `guardarAbono`
const guardarAbono = async () => {
    if (!clienteStore.clienteActivo)
        return alert("Selecciona un cliente primero");

    try {
        await axios.post(
            `/api/clientes/${clienteStore.clienteActivo.id}/abonos`,
            abono.value
        );

        // Limpiar el campo
        abono.value.monto = "";

        // ✅ Notificación de éxito con SweetAlert2
        await Swal.fire({
            icon: "success",
            title: "Abono registrado",
            text: "El abono fue guardado correctamente.",
            timer: 2000,
            showConfirmButton: false,
        });

        // 🔄 Recargar resumen directamente aquí (sin emitir)
        const { data } = await axios.get(
            `/api/clientes/${clienteStore.clienteActivo.id}/resumen`
        );
        // Actualizar el cliente activo con los nuevos datos
        clienteStore.clienteActivo = {
            ...clienteStore.clienteActivo,
            ...data,
        };

    } catch (error) {
        console.error("Error al guardar abono:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo guardar el abono.",
        });
    }
};
</script>

