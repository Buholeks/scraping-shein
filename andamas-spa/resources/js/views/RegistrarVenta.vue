<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold">🛒 Nueva Venta</h2>

        <!-- 🔍 Buscar productos -->
        <input
            v-model="busqueda"
            @input="buscarProductos"
            placeholder="Buscar por título o SKU"
            class="border p-2 rounded w-full sm:w-96"
        />

        <ul
            v-if="resultados.length"
            class="bg-white border mt-2 rounded shadow max-h-48 overflow-y-auto"
        >
            <li
                v-for="producto in resultados"
                :key="producto.id"
                @click="agregarProducto(producto)"
                class="px-4 py-2 hover:bg-blue-100 cursor-pointer"
            >
                {{ producto.titulo }} – {{ producto.sku }} —
                {{ producto.cliente?.nombre || "Sin cliente" }}
            </li>
        </ul>

        <!-- 🧾 Tabla de productos a vender -->
        <table
            v-if="productosVenta.length"
            class="w-full text-sm text-left text-gray-700 bg-white rounded-lg shadow overflow-hidden"
        >
            <thead
                class="bg-gray-400 text-gray-200 uppercase text-xs tracking-wider"
            >
                <tr>
                    <th class="px-4 py-3">Imagen</th>
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-4 py-3">SKU</th>
                    <th class="px-4 py-3 text-right">Precio</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr
                    v-for="(producto, index) in productosVenta"
                    :key="producto.id"
                    class="hover:bg-gray-50 transition-colors"
                >
                    <td class="px-4 py-3">
                        <img
                            :src="producto.imagen"
                            alt="Imagen del producto"
                            class="w-12 h-12 object-cover rounded border border-gray-300"
                        />
                    </td>
                    <td class="px-4 py-3 font-medium">{{ producto.titulo }}</td>
                    <td class="px-4 py-3">{{ producto.sku }}</td>
                    <td
                        class="px-4 py-3 text-right font-semibold text-green-600"
                    >
                        ${{ parseFloat(producto.precio || 0).toFixed(2) }}
                    </td>
                    <td class="px-4 py-3">
                        {{ producto.cliente?.nombre || "—" }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button
                            @click="eliminarProducto(index)"
                            class="text-red-600 hover:text-red-800 transition font-medium flex items-center justify-center gap-1"
                        >
                            <fa icon="trash-can" />
                            <span>Eliminar</span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 💳 Info adicional de venta -->
        <div v-if="productosVenta.length" class="space-y-4 border-t pt-4">
            <div>
                <label class="font-semibold">Forma de pago</label>
                <select
                    v-model="formaPago"
                    class="border p-2 rounded w-full sm:w-64 mt-1"
                >
                    <option value="">Selecciona una forma de pago</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <p class="text-lg">
                Total productos:
                <span class="font-medium">${{ total.toFixed(2) }}</span>
            </p>
            <p class="text-lg text-green-600">
                Abono disponible: -${{ abonoDisponible.toFixed(2) }}
            </p>
            <p class="text-lg font-bold text-blue-700">
                Total a pagar: ${{ totalAPagar.toFixed(2) }}
            </p>

            <button
                class="mt-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                @click="confirmarVenta"
            >
                Confirmar venta
            </button>
        </div>
    </div>

    <div
        v-if="mostrarResumen"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md space-y-4"
        >
            <h3 class="text-xl font-bold">🧾 Resumen de venta</h3>
            <p><strong>Cliente:</strong> {{ resumenVenta.cliente.nombre }}</p>

            <ul class="text-sm space-y-1">
                <li
                    v-for="producto in resumenVenta.productos"
                    :key="producto.id"
                >
                    - {{ producto.titulo }} (${{
                        parseFloat(producto.precio).toFixed(2)
                    }})
                </li>
            </ul>

            <p><strong>Total:</strong> ${{ resumenVenta.total.toFixed(2) }}</p>
            <p>
                <strong>Abono aplicado:</strong> -${{
                    resumenVenta.abono_aplicado.toFixed(2)
                }}
            </p>
            <p>
                <strong>Total pagado:</strong> ${{
                    resumenVenta.total_pagado.toFixed(2)
                }}
            </p>
            <p><strong>Forma de pago:</strong> {{ resumenVenta.forma_pago }}</p>

            <div class="text-right">
                <button
                    @click="mostrarResumen = false"
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Cerrar
                </button>

                <a
                    :href="`https://wa.me/52${
                        resumenVenta.cliente.telefono
                    }?text=${generarMensajeWhatsApp()}`"
                    target="_blank"
                    class="mt-2 inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                >
                    📲 Enviar por WhatsApp
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import axios from "axios";

const busqueda = ref("");
const resultados = ref([]);
const productosVenta = ref([]);
const formaPago = ref("");
const abonoDisponible = ref(0);

const mostrarResumen = ref(false);
const resumenVenta = ref({
    cliente: null,
    productos: [],
    total: 0,
    abono_aplicado: 0,
    total_pagado: 0,
    forma_pago: "",
});

const total = computed(() => {
    return productosVenta.value.reduce((sum, p) => {
        const precio = Number(p?.precio);
        return sum + (Number.isFinite(precio) ? precio : 0);
    }, 0);
});

const totalAPagar = computed(() => {
    return Math.max(0, total.value - abonoDisponible.value);
});

const buscarProductos = async () => {
    if (busqueda.value.length < 2) {
        resultados.value = [];
        return;
    }

    const { data } = await axios.get("/api/productos/disponible", {
        params: { buscar: busqueda.value },
    });

    resultados.value = data.filter((p) => {
        return !productosVenta.value.some((agregado) => agregado.id === p.id);
    });
};

const generarMensajeWhatsApp = () => {
    const c = resumenVenta.value.cliente;
    const p = resumenVenta.value.productos;

    // Texto de productos
    const productosTexto = p
        .map(
            (prod) =>
                `- ${prod.titulo} ($${parseFloat(prod.precio).toFixed(2)})`
        )
        .join("%0A"); // %0A = salto de línea en URL

    // Mensaje completo (sin emojis ni caracteres raros)
    const mensaje =
        `Hola ${c.nombre}, gracias por tu compra.%0A%0A` +
        `Resumen de tu venta:%0A` +
        `${productosTexto}%0A%0A` +
        `Total: $${resumenVenta.value.total.toFixed(2)}%0A` +
        `Abono aplicado: $${resumenVenta.value.abono_aplicado.toFixed(2)}%0A` +
        `Total a pagar: $${resumenVenta.value.total_pagado.toFixed(2)}%0A` +
        `Forma de pago: ${resumenVenta.value.forma_pago}%0A%0A` +
        `¡Gracias por tu preferencia!`;

    return mensaje;
};

const obtenerAbonoCliente = async (clienteId) => {
    try {
        const { data } = await axios.get(`/api/clientes/${clienteId}/abono`);
        abonoDisponible.value = Number(data.abono || 0);
    } catch (error) {
        console.error("❌ Error al obtener abono:", error);
        abonoDisponible.value = 0;
    }
};

const agregarProducto = (producto) => {
    if (!productosVenta.value.find((p) => p.id === producto.id)) {
        productosVenta.value.push(producto);

        if (productosVenta.value.length === 1 && producto.cliente?.id) {
            obtenerAbonoCliente(producto.cliente.id);
        }
    }

    busqueda.value = "";
    resultados.value = [];
};

const eliminarProducto = (index) => {
    productosVenta.value.splice(index, 1);
};

const confirmarVenta = async () => {
    // Validaciones por separado para mejor feedback
    if (!productosVenta.value.length && !formaPago.value) {
        await Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Debes agregar al menos un producto y seleccionar una forma de pago.',
            confirmButtonText: 'Entendido',
        });
        return;
    }

    if (!productosVenta.value.length) {
        await Swal.fire({
            icon: 'warning',
            title: 'Productos faltantes',
            text: 'Debes agregar al menos un producto antes de confirmar la venta.',
            confirmButtonText: 'Entendido',
        });
        return;
    }

    if (!formaPago.value) {
        await Swal.fire({
            icon: 'warning',
            title: 'Forma de pago no seleccionada',
            text: 'Selecciona una forma de pago antes de confirmar la venta.',
            confirmButtonText: 'Seleccionar forma de pago',
        });
        return;
    }

    const cliente = productosVenta.value[0]?.cliente;

    if (!cliente?.id) {
        await Swal.fire({
            icon: 'error',
            title: 'Cliente no definido',
            text: 'No se puede registrar la venta porque el cliente no está definido.',
            confirmButtonText: 'Ok',
        });
        return;
    }

    // Confirmación previa
    const resultado = await Swal.fire({
        title: '¿Confirmar venta?',
        html: `
            <div style="text-align: left;">
                <p><strong>Cliente:</strong> ${cliente.nombre}</p>
                <p><strong>Total a pagar:</strong> $${totalAPagar.value.toFixed(2)}</p>
                <p><strong>Forma de pago:</strong> ${formaPago.value}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
    });

    if (!resultado.isConfirmed) return;

    try {
        const response = await axios.post("/api/ventas/confirmar", {
            productos: productosVenta.value.map((p) => p.id),
            forma_pago: formaPago.value,
            id_cliente: cliente.id,
        });

        resumenVenta.value = {
            cliente,
            productos: [...productosVenta.value],
            total: total.value,
            abono_aplicado: response.data.abono_aplicado,
            total_pagado: response.data.total_pagado,
            forma_pago: formaPago.value,
        };

        mostrarResumen.value = true;

        productosVenta.value = [];
        formaPago.value = "";
        abonoDisponible.value = 0;

        await Swal.fire({
            icon: 'success',
            title: 'Venta registrada',
            text: 'La venta se ha registrado correctamente.',
            timer: 2000,
            showConfirmButton: false,
        });

    } catch (error) {
        console.error("❌ Error al registrar la venta:", error.response?.data || error);

        await Swal.fire({
            icon: 'error',
            title: 'Error en la venta',
            text: 'Hubo un problema al registrar la venta. Verifica que los datos sean válidos.',
            confirmButtonText: 'Cerrar',
        });
    }
};

</script>

<style scoped>
input:focus,
select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4);
}
</style>
