<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold"> Productos Pendiente de Recibir</h2>

        <!-- Buscador -->
        <input
            v-model="busqueda"
            @input="cargarProductos"
            placeholder="Buscar por título o SKU"
            class="border rounded p-2 w-full sm:w-96"
        />

        <table
            class="w-full mt-6 text-sm text-gray-700 rounded-xl overflow-hidden shadow-lg border border-gray-200"
        >
            <thead
                class="bg-gray-300 text-gray-600 text-xs uppercase tracking-wide"
            >
                <tr>
                    <th class="px-4 py-3 text-center">Imagen</th>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-left">Precio</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <tr
                    v-for="producto in productos"
                    :key="producto.id"
                    class="hover:bg-gray-50 transition-colors duration-200 border-t border-gray-100"
                >
                    <td class="px-4 py-2 text-center">
                        <img
                            :src="producto.imagen"
                            alt="Producto"
                            class="w-14 h-14 object-cover rounded-md border border-gray-300 shadow-sm"
                        />
                    </td>
                    <td class="px-4 py-2 font-medium">{{ producto.titulo }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ producto.sku }}</td>
                    <td class="p-1">
                        {{
                            new Intl.NumberFormat("en-US", {
                                style: "currency",
                                currency: "USD",
                            }).format(producto.precio || 0)
                        }}
                    </td>
                    <td class="px-4 py-2 capitalize">
                        <span
                            class="inline-block px-2 py-1 text-xs rounded-full"
                            :class="{
                                'bg-green-100 text-green-700':
                                    producto.estado === 'en_tienda',
                                'bg-yellow-100 text-yellow-700':
                                    producto.estado === 'pendiente',
                                'bg-red-100 text-red-700':
                                    producto.estado === 'en_camino',
                            }"
                        >
                            {{ producto.estado.replace("_", " ") }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button
                            @click="marcarEnTienda(producto)"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-1.5 rounded-full shadow-sm transition-all duration-200"
                        >
                            Marcar como en tienda
                        </button>
                    </td>
                </tr>

                <tr v-if="productos.length === 0">
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                        💤 No hay productos por Recibir.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const productos = ref([]);
const busqueda = ref("");

const cargarProductos = async () => {
    const { data } = await axios.get("/api/productos/noRecogidos", {
        params: { buscar: busqueda.value },
    });
    productos.value = data;
};

const marcarEnTienda = async (producto) => {
    try {
        const { data } = await axios.put(
            `/api/productos/${producto.id}/estado`,
            {
                estado: "en_tienda",
            }
        );

        alert(data.mensaje); // 💬 Mostrar el mensaje al usuario
        await cargarProductos(); // Recargar lista
    } catch (error) {
        console.error("Error al actualizar estado:", error);
        alert("Hubo un error al actualizar el estado");
    }
};

onMounted(cargarProductos);
</script>
