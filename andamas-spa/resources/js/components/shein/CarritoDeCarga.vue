<template>
    <div v-if="productos.length" class="mt-6">
        <h3 class="text-xl font-semibold mb-2">🛒 Productos en la lista</h3>
        <table class="min-w-full border rounded bg-white">
            <thead class="bg-pink-100">
                <tr>
                    <th class="p-2">Imagen</th>
                    <th class="p-2 text-left">SKU</th>
                    <th class="p-2 text-left">Título</th>
                    <th class="p-2 text-left">Precio</th>
                    <th class="p-2 text-left  text-red-400 line-through">Precio</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in productos" :key="p.sku" class="border-t">
                    <!-- 👇 Imagen del producto -->
                    <td class="p-1">
                        <img
                            :src="p.images[0]"
                            alt="img"
                            class="h-16 rounded shadow"
                        />
                    </td>

                    <td class="p-1">{{ p.sku }}</td>
                    <td class="p-1">{{ p.title }}</td>
                    <td class="p-1">{{ p.price ?? "N/A" }}</td>
                    <td class="p-1 text-red-400 line-through">{{ p.price_original ?? "N/A" }}</td>
                    <td class="p-1">
                        <button
                            @click="$emit('eliminar', p.sku)"
                            class="text-red-600 hover:underline"
                        >
                            Eliminar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Botón guardar todo -->
        <button
            @click="$emit('guardar-todos')"
            class="mt-4 bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800"
        >
            Guardar todos
        </button>
    </div>
</template>

<script setup>
defineProps(["productos"]);
defineEmits(["eliminar", "guardar-todos"]);
</script>
