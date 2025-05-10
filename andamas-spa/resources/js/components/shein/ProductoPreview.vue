<template>
    <div class="bg-white p-4 rounded shadow space-y-4">
        <h3 class="text-xl font-semibold">Producto encontrado:</h3>
        <p><strong>SKU:</strong> {{ producto.sku }}</p>
        <p><strong>Título:</strong> {{ producto.title }}</p>

        <!-- Precio original (solo lectura) -->
        <div v-if="producto.price_original" class="w-100">
            <label class="block text-sm font-medium text-gray-700">Precio original:</label>
            <input
                :value="producto.price_original"
                type="text"
                class="line-through mt-1 block w-full border border-red-300 rounded-md shadow-sm p-2 bg-red-50 cursor-not-allowed"
                readonly
            />
        </div>

        <!-- Precio actual (editable) -->
        <div class="flex">
            <div class="w-50 mr-2">
                <label class="block text-sm font-medium text-gray-700">Precio actual:</label>
                <input
                    v-model="producto.price"
                    type="text"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="No disponible"
                    @blur="formatPrice('price')"
                    @input="calculateCommission"
                />
                <p class="mt-1 text-xs text-gray-500">Puedes editar si quieres</p>
            </div>

            <!-- Comisión (calculada automáticamente pero editable) -->
            <div class="w-50 mr-2">
                <label class="block text-sm font-medium text-gray-700">Comisión:</label>
                <input
                    v-model="producto.commission"
                    type="text"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                    @blur="formatPrice('commission')"
                    @input="calculateTotal"
                />
                <p class="mt-1 text-xs text-gray-500">Comisión calculada automáticamente según el precio (editable)</p>
            </div>

            <!-- Total (precio + comisión) -->
            <div class="w-50">
                <label class="block text-sm font-medium text-gray-700">Total:</label>
                <div class="mt-1 p-2 bg-gray-50 rounded-md border border-gray-200">
                    <span class="font-semibold">{{ formattedTotal }}</span>
                </div>
            </div>
        </div>

        <!-- Galería de imágenes -->
        <div class="my-3 flex gap-2 flex-wrap">
            <img
                v-for="img in producto.images"
                :key="img"
                :src="img"
                alt="Imagen del producto"
                class="h-24 rounded shadow"
            />
        </div>

        <!-- Botón de agregar -->
        <button
            @click="$emit('agregar')"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
            Agregar producto
        </button>
    </div>
</template>

<script setup>
import { computed, watch } from "vue";

const props = defineProps(["producto"]);
const emit = defineEmits(["agregar"]);

const commissionRules = [
    { min: 0, max: 100, value: 5 },
    { min: 101, max: 200, value: 10 },
    { min: 201, max: 300, value: 15 },
    { min: 301, max: 1000, value: 20 },
    { min: 1001, max: Infinity, value: 30 },
];

// Formateador de precios
const cleanAndFormatPrice = (price) => {
    if (!price) return "$0.00";
    const cleaned = price.toString().replace(/[^\d.]/g, "");
    const num = parseFloat(cleaned) || 0;
    return `$${num.toFixed(2)}`;
};

// Inicializa precio y comisión
if (props.producto.price) {
    props.producto.price = cleanAndFormatPrice(props.producto.price);
}
if (!props.producto.commission) {
    props.producto.commission = "$0.00";
}

// Calcula la comisión
const calculateCommission = () => {
    const priceValue = parseFloat(props.producto.price?.replace(/[^\d.]/g, "")) || 0;
    const rule = commissionRules.find((r) => priceValue >= r.min && priceValue <= r.max);
    props.producto.commission = `$${(rule?.value || 0).toFixed(2)}`;
    calculateTotal(); // recalcular total al cambiar comisión
};

// Formatea el campo con símbolo $
const formatPrice = (field) => {
    if (props.producto[field]) {
        const cleaned = props.producto[field].replace(/[^\d.]/g, "");
        const num = parseFloat(cleaned) || 0;
        props.producto[field] = `$${num.toFixed(2)}`;
        calculateTotal(); // recalcular total al formatear
    }
};

// Guarda el total dentro del producto
const calculateTotal = () => {
    const price = parseFloat(props.producto.price?.replace(/[^\d.]/g, "")) || 0;
    const commission = parseFloat(props.producto.commission?.replace(/[^\d.]/g, "")) || 0;
    props.producto.total = price + commission;
};

// Formato bonito del total
const formattedTotal = computed(() => {
    return `$${(props.producto.total || 0).toFixed(2)}`;
});

// Recalcular cuando cambie el precio
watch(
    () => props.producto.price,
    () => {
        calculateCommission();
    }
);

// Calcular valores iniciales
calculateCommission();
calculateTotal();
</script>
