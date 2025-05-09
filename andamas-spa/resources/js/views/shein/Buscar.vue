<template>
    <div>
        <!-- 🔍 BUSCADOR DE CLIENTE -->
        <div class="flex justify-between items-start gap-4">
            <h2 class="text-2xl font-bold text-gray-800">
                Buscar producto de Shein
            </h2>

            <div class="w-96 mb-6 relative">
                <div class="relative">
                    <input
                        v-model="busquedaCliente"
                        @input="onBuscarClientes"
                        placeholder="Buscar Cliente: Nombre o teléfono"
                        class="border border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none rounded-lg w-full p-2 pl-10"
                    />
                    <fa
                        icon="search"
                        class="absolute left-3 top-3 text-gray-400"
                    />
                </div>

                <!-- Resultados de búsqueda -->
                <ul
                    v-if="resultadosClientes.length > 0"
                    class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                >
                    <li
                        v-for="cliente in resultadosClientes"
                        :key="cliente.id"
                        @click="seleccionarCliente(cliente)"
                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                    >
                        <div class="font-medium">{{ cliente.nombre }}</div>
                        <div class="text-sm text-gray-600">
                            {{ cliente.telefono }}
                        </div>
                    </li>
                </ul>

                <!-- Cliente seleccionado -->
                <div
                    v-if="clienteSeleccionado"
                    class="mt-2 p-2 bg-green-50 rounded-lg flex justify-between items-center"
                >
                    <div>
                        <span class="text-green-700"
                            >✅ Cliente seleccionado:</span
                        >
                        <span class="font-semibold ml-1">{{
                            clienteSeleccionado.nombre
                        }}</span>
                    </div>
                    <button
                        @click="clienteSeleccionado = null"
                        class="text-red-500 hover:text-red-700 p-1"
                    >
                        <fa icon="times" />
                    </button>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE BÚSQUEDA DE PRODUCTO -->
        <form @submit.prevent="ejecutarBusqueda(modo)" class="mb-6">
            <div class="flex gap-3 items-start">
                <!-- Contenedor flex principal -->
                <div class="relative flex-grow">
                    <!-- Input ahora ocupa el espacio disponible -->
                    <input
                        v-model="url"
                        type="text"
                        placeholder="Pega aquí la URL completa del producto de SHEIN"
                        class="w-full border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg p-3 pl-10"
                        :class="{ 'border-red-500': errorUrl }"
                    />
                    <fa
                        icon="link"
                        class="absolute left-3 top-3.5 text-gray-400"
                    />

                    <!-- Mensaje de error ahora dentro del contenedor del input -->
                    <div v-if="errorUrl" class="mt-1 text-red-600 text-sm">
                        {{ errorUrl }}
                    </div>
                </div>

                <!-- Botón ahora al lado del input -->
                <button
                    type="button"
                    @click="
                        modo = 'original';
                        ejecutarBusqueda('original');
                    "
                    class="flex-shrink-0 flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors h-[42px]"
                    :disabled="cargando"
                    :class="{ 'opacity-50 cursor-not-allowed': cargando }"
                >
                    <fa
                        v-if="cargando && modo === 'original'"
                        icon="spinner"
                        spin
                    />
                    <fa v-else icon="magnifying-glass" />
                    Buscar Producto
                </button>
            </div>
        </form>

        <!-- ESTADOS DE CARGA -->
        <div
            v-if="cargando"
            class="mb-6 p-4 bg-blue-50 rounded-lg flex items-center gap-3"
        >
            <fa icon="spinner" spin class="text-blue-500" />
            <span class="text-blue-700"
                >Buscando producto, por favor espera...</span
            >
        </div>

        <!-- NOTIFICACIONES -->
        <Notification
            v-if="notificacion.mostrar"
            :tipo="notificacion.tipo"
            @cerrar="notificacion.mostrar = false"
            class="mb-6"
        >
            {{ notificacion.mensaje }}
        </Notification>

        <!-- PRODUCTO ENCONTRADO -->
        <ProductoPreview
            v-if="producto && producto.sku"
            :producto="producto"
            @agregar="agregarProducto"
            :cargando="cargando"
        />

        <!-- CARRITO DE PRODUCTOS -->
        <CarritoDeCarga
            v-if="listaProductos.length > 0"
            :productos="listaProductos"
            @eliminar="eliminarDeLista"
            @guardar-todos="guardarTodos"
            :guardando="guardando"
        />
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "@/stores/authStore";
import { debounce } from "lodash-es";
import ProductoPreview from "@/components/shein/ProductoPreview.vue";
import CarritoDeCarga from "@/components/shein/CarritoDeCarga.vue";
import Notification from "@/components/shein/Notification.vue";

const auth = useAuthStore();

// Configuración
const PYTHON_API = import.meta.env.VITE_PYTHON_API || "http://localhost:5000";

// Estados
const url = ref("");
const modo = ref("estimated");
const producto = ref(null);
const errorUrl = ref("");
const cargando = ref(false);
const guardando = ref(false);
const listaProductos = ref([]);

// Búsqueda de clientes
const busquedaCliente = ref("");
const resultadosClientes = ref([]);
const clienteSeleccionado = ref(null);

// Notificaciones
const notificacion = ref({
    mostrar: false,
    tipo: "exito",
    mensaje: "",
});

// Cargar carrito guardado
onMounted(() => {
    const carritoGuardado = localStorage.getItem("shein_carrito");
    if (carritoGuardado) {
        listaProductos.value = JSON.parse(carritoGuardado);
    }
});

// Búsqueda debounceada de clientes
const onBuscarClientes = debounce(async () => {
    if (busquedaCliente.value.length < 2) {
        resultadosClientes.value = [];
        return;
    }

    try {
        const { data } = await axios.get("/api/clientes", {
            params: { search: busquedaCliente.value },
        });
        resultadosClientes.value = data;
    } catch (error) {
        mostrarNotificacion("error", "Error buscando clientes");
    }
}, 300);

// Seleccionar cliente
const seleccionarCliente = (cliente) => {
    clienteSeleccionado.value = cliente;
    busquedaCliente.value = `${cliente.nombre} - ${cliente.telefono}`;
    resultadosClientes.value = [];
};

// Mostrar notificación
const mostrarNotificacion = (tipo, mensaje, duracion = 5000) => {
    notificacion.value = {
        mostrar: true,
        tipo,
        mensaje,
    };

    if (duracion) {
        setTimeout(() => {
            notificacion.value.mostrar = false;
        }, duracion);
    }
};

// Validar URL de SHEIN
const validarUrlShein = (url) => {
    return /^(https?:\/\/)?(www\.)?shein\.com\.mx\/.+/i.test(url);
};

// Buscar producto
const ejecutarBusqueda = async (modoBusqueda) => {
    errorUrl.value = "";
    producto.value = null;
    cargando.value = true;

    try {
        // Validación
        if (!url.value) {
            errorUrl.value = "La URL es requerida";
            return;
        }

        if (!validarUrlShein(url.value)) {
            errorUrl.value = "Por favor ingresa una URL válida de SHEIN México";
            return;
        }

        // Llamada al servidor Python
        const endpoint = `${PYTHON_API}/scraper_${modoBusqueda}`;
        const { data } = await axios.post(
            endpoint,
            { url: url.value },
            {
                timeout: 30000, // 30 segundos timeout
            }
        );

        if (!data.sku) {
            errorUrl.value = "No se pudo obtener información del producto";
            return;
        }

        producto.value = data;
        mostrarNotificacion("exito", "Producto encontrado con éxito");
    } catch (error) {
        console.error("Error en scraping:", error);

        if (error.code === "ECONNABORTED") {
            errorUrl.value = "El servidor tardó demasiado en responder";
        } else {
            errorUrl.value =
                error.response?.data?.message ||
                "Error al obtener información del producto";
        }
    } finally {
        cargando.value = false;
    }
};

// Agregar producto al carrito
const agregarProducto = () => {
    if (!producto.value) return;

    const existe = listaProductos.value.some(
        (p) => p.sku === producto.value.sku
    );
    if (existe) {
        mostrarNotificacion("error", "Este producto ya está en el carrito");
        return;
    }

    listaProductos.value.push({ ...producto.value });
    producto.value = null;
    url.value = "";

    // Persistir en localStorage
    localStorage.setItem("shein_carrito", JSON.stringify(listaProductos.value));

    mostrarNotificacion("exito", "Producto agregado al carrito");
};

// Eliminar producto del carrito
const eliminarDeLista = (sku) => {
    listaProductos.value = listaProductos.value.filter((p) => p.sku !== sku);
    localStorage.setItem("shein_carrito", JSON.stringify(listaProductos.value));
    mostrarNotificacion("exito", "Producto eliminado del carrito");
};

// Limpiar precio
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

// Guardar todos los productos
const guardarTodos = async () => {
    if (listaProductos.value.length === 0) {
        mostrarNotificacion("error", "No hay productos para guardar");
        return;
    }

    if (!auth.sucursalActual?.id || !auth.idEmpresa) {
        mostrarNotificacion("error", "No hay sucursal o empresa seleccionada");
        return;
    }

    guardando.value = true;

    try {
        const productosAdaptados = listaProductos.value.map((p) => ({
            sku: p.sku,
            title: (p.title || p.titulo)?.substring(0, 255),
            price: limpiarPrecio(p.price),
            price_original: limpiarPrecio(p.price_original),
            images: p.images ?? [],
        }));

        await axios.post("/api/shein/guardar-lote", {
            productos: productosAdaptados,
            id_empresa: auth.idEmpresa,
            id_sucursal: auth.sucursalActual.id,
            id_cliente: clienteSeleccionado.value?.id,
        });

        // Limpiar después de guardar
        listaProductos.value = [];
        localStorage.removeItem("shein_carrito");

        mostrarNotificacion("exito", "Productos guardados correctamente");
    } catch (error) {
        console.error("Error al guardar:", error);
        mostrarNotificacion(
            "error",
            error.response?.data?.message || "Error al guardar productos"
        );
    } finally {
        guardando.value = false;
    }
};

// Watchers
watch(busquedaCliente, (nuevoValor) => {
    if (
        !nuevoValor ||
        (clienteSeleccionado.value &&
            !nuevoValor.includes(clienteSeleccionado.value.nombre))
    ) {
        clienteSeleccionado.value = null;
    }
});
</script>
