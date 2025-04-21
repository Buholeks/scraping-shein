<template>
    <div>
        <!-- 🔍 BUSCADOR DE CLIENTE -->
        <div class="flex justify-between items-start gap-4">
            <h2 class="text-2xl font-bold">Buscar producto de Shein</h2>
            <div class="w-96 mb-6 relative">
                <input
                    v-model="busquedaCliente"
                    @input="buscarClientes"
                    placeholder="Buscar Cliente por: Nombre o telefono"
                    class="border border-blue-400 focus:border-blue-500 focus:outline-none rounded w-full block"
                />

                <!-- Resultados de búsqueda -->
                <ul
                    v-if="resultadosClientes.length"
                    class="absolute border border-gray-300 mt-1 bg-white rounded shadow max-h-40 overflow-y-auto"
                >
                    <li
                        v-for="cliente in resultadosClientes"
                        :key="cliente.id"
                        @click="seleccionarCliente(cliente)"
                        class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                    >
                        {{ cliente.nombre }} – {{ cliente.telefono }}
                    </li>
                </ul>

                <!-- Cliente seleccionado -->
                <div
                    v-if="clienteSeleccionado"
                    class="mt-2 text-sm text-green-600"
                >
                    ✅ Cliente seleccionado:
                    <strong>{{ clienteSeleccionado.nombre }}</strong>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE BÚSQUEDA DE PRODUCTO -->
        <form @submit.prevent="buscar">
            <input
                v-model="url"
                type="text"
                placeholder="URL del producto"
                class="w-full border p-2 rounded"
                :class="{ 'border-red-500': errorUrl }"
            />
            <div v-if="errorUrl" class="text-red-600">{{ errorUrl }}</div>

            <div class="flex gap-2 mt-3">
                <button
                    type="button"
                    @click="ejecutarBusqueda('estimated')"
                    class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer"
                >
                    <fa icon="magnifying-glass" /> Buscar (Estimado)
                </button>
                <button
                    type="button"
                    @click="ejecutarBusqueda('original')"
                    class="bg-yellow-500 text-white px-4 py-2 rounded cursor-pointer"
                >
                    <fa icon="magnifying-glass" /> Buscar (Original)
                </button>
            </div>
        </form>

        <!-- PRODUCTO ENCONTRADO -->
        <ProductoPreview
            v-if="producto && producto.sku"
            :producto="producto"
            @agregar="agregarProducto"
        />

        <!-- CARRITO DE PRODUCTOS -->
        <CarritoDeCarga
            :productos="listaProductos"
            @eliminar="eliminarDeLista"
            @guardar-todos="guardarTodos"
        />

        <!-- MENSAJE -->
        <div v-if="mensaje" class="mt-4 text-green-600 font-semibold">
            {{ mensaje }}
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import { useAuthStore } from "@/stores/authStore";
import ProductoPreview from "@/components/shein/ProductoPreview.vue";
import CarritoDeCarga from "@/components/shein/CarritoDeCarga.vue";

const auth = useAuthStore();

const url = ref("");
const modo = ref("estimated");
const producto = ref(null);
const errorUrl = ref("");
const mensaje = ref("");
const listaProductos = ref([]);

// 🧠 Cliente seleccionado y búsqueda
const busquedaCliente = ref("");
const resultadosClientes = ref([]);
const clienteSeleccionado = ref(null);

// 🔍 Buscar clientes en tiempo real
const buscarClientes = async () => {
    if (busquedaCliente.value.length < 2) {
        resultadosClientes.value = [];
        return;
    }

    try {
        const { data } = await axios.get("/api/clientes", {
            params: {
                search: busquedaCliente.value,
            },
        });
        resultadosClientes.value = data;
    } catch (error) {
        console.error("Error buscando clientes:", error);
    }
};

// ✅ Cambiar cliente
const seleccionarCliente = (cliente) => {
    clienteSeleccionado.value = cliente;
    busquedaCliente.value = cliente.nombre;
    resultadosClientes.value = [];
};

// 🔄 Si el texto cambia, limpiar selección previa
watch(busquedaCliente, (nuevo) => {
    if (
        clienteSeleccionado.value &&
        nuevo !== clienteSeleccionado.value.nombre
    ) {
        clienteSeleccionado.value = null;
    }
});

// 🛒 Agregar producto a la lista
const agregarProducto = () => {
    if (!producto.value) return;

    const existe = listaProductos.value.find(
        (p) => p.sku === producto.value.sku
    );
    if (existe) {
        mensaje.value = "Este producto ya fue agregado.";
        return;
    }

    listaProductos.value.push({ ...producto.value });
    producto.value = null;
    url.value = "";
    mensaje.value = "";
};

// ❌ Eliminar producto de la lista
const eliminarDeLista = (sku) => {
    listaProductos.value = listaProductos.value.filter((p) => p.sku !== sku);
};

// 🧼 Limpiar precio
const limpiarPrecio = (valor) => {
    if (!valor || valor === "N/A") return null;
    const num = parseFloat(
        valor
            .toString()
            .replace(/[^0-9.,]/g, "")
            .replace(",", ".")
    );
    return isNaN(num) ? null : num;
};

// 🧠 Buscar producto desde backend Python
const buscar = async () => {
    errorUrl.value = "";
    mensaje.value = "";
    producto.value = null;

    if (!url.value) {
        errorUrl.value = "La URL es requerida";
        return;
    }

    try {
        const endpoint =
            modo.value === "original"
                ? "http://127.0.0.1:5000/scraper_original"
                : "http://127.0.0.1:5000/scraper_estimated";

        const { data } = await axios.post(endpoint, { url: url.value });

        if (!data.sku) {
            errorUrl.value = "Producto inválido o no disponible.";
            return;
        }

        producto.value = data;
    } catch (e) {
        errorUrl.value = "No se pudo obtener la información del producto.";
    }
};

// 📦 Guardar todos los productos en la BD
const guardarTodos = async () => {
    mensaje.value = "";

    if (!auth.sucursalActual?.id || !auth.idEmpresa) {
        mensaje.value = "No hay sucursal o empresa seleccionada.";
        return;
    }

    const adaptados = listaProductos.value.map((p) => ({
        sku: p.sku,
        title: (p.title || p.titulo)?.substring(0, 255),
        price: limpiarPrecio(p.price),
        price_original: limpiarPrecio(p.price_original),
        images: p.images ?? [],
    }));

    try {
        await axios.post("/api/shein/guardar-lote", {
            productos: adaptados,
            id_empresa: auth.idEmpresa,
            id_sucursal: auth.sucursalActual.id,
            id_cliente: clienteSeleccionado.value?.id,
        });

        mensaje.value = "Todos los productos fueron guardados con éxito.";
        listaProductos.value = [];
    } catch (e) {
        console.error("Error al guardar productos:", e);
        console.log("Detalles del error:", e.response?.data?.errors);
        mensaje.value = "Error al guardar los productos.";
    }
};

// 🔀 Ejecuta búsqueda según modo seleccionado
const ejecutarBusqueda = (modoSeleccionado) => {
    modo.value = modoSeleccionado;
    buscar();
};
</script>
