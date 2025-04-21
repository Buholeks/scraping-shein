<template>
    <div>
        <!-- Botón hamburguesa (solo en móvil) -->
        <button
            @click="toggleSidebar"
            class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded focus:outline-none"
        >
            ☰
        </button>

        <!-- Overlay oscuro cuando el menú está abierto en móvil -->
        <div
            v-if="isOpen"
            @click="closeSidebar"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
        ></div>

        <!-- Sidebar -->
        <div
            :class="[
                'fixed top-0 left-0 h-full w-50 bg-gray-800 text-white z-50 transition-transform duration-300 ease-in-out',
                { '-translate-x-full': !isOpen },
                'md:translate-x-0 md:relative md:block',
            ]"
        >
            <div class="pt-15 p-5 flex flex-col divide-y divide-gray-400">
                <router-link
                    to="/app"
                    class="block mb-3 hover:bg-gray-700 p-2 rounded"
                    @click="closeSidebar"
                >
                    <fa icon="house" class="mr-2" />
                    Inicio
                </router-link>

                <router-link
                    to="/ventas"
                    class="block mb-3 hover:bg-gray-700 p-2 rounded"
                    @click="closeSidebar"
                >
                    <fa icon="barcode" class="mr-2" />
                    Nueva Venta
                </router-link>

                <router-link
                    to="/clientes"
                    class="block mb-3 hover:bg-gray-700 p-2 rounded divide-y divide-gray-100"
                    @click="closeSidebar"
                >
                    <fa icon="users" class="mr-2" />
                    Clientes
                </router-link>

                <router-link
                    to="/productos-entrega"
                    class="block mb-3 hover:bg-gray-700 p-2 rounded"
                    @click="closeSidebar"
                >
                    <fa icon="people-carry-box" class="mr-2" />
                    Recibir
                </router-link>

                <router-link
                    to="/shein"
                    class="block mb-3 hover:bg-gray-700 p-2 rounded"
                    @click="closeSidebar"
                >
                    <fa icon="s" class="mr-2" />
                    Buscar Shein
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";

const isOpen = ref(false);

const toggleSidebar = () => {
    isOpen.value = !isOpen.value;
};

const closeSidebar = () => {
    if (window.innerWidth < 768) {
        isOpen.value = false;
    }
};

onMounted(() => {
    // Abierto por defecto en escritorio
    if (window.innerWidth >= 768) {
        isOpen.value = true;
    }
});
</script>

<style scoped>
/* para evitar desplazamiento cuando sidebar está abierto */
body {
    overflow-x: hidden;
}
</style>
