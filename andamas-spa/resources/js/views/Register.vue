<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <form
            @submit.prevent="register"
            class="bg-white p-8 rounded shadow-md w-96"
        >
            <h2 class="text-2xl font-bold mb-4">Registro de Usuario</h2>

            <input
                v-model="form.name"
                type="text"
                placeholder="Nombre"
                class="w-full mb-3 p-2 border rounded"
            />
            <input
                v-model="form.email"
                type="email"
                placeholder="Correo"
                class="w-full mb-3 p-2 border rounded"
            />
            <input
                v-model="form.password"
                type="password"
                placeholder="Contraseña"
                class="w-full mb-3 p-2 border rounded"
            />
            <input
                v-model="form.password_confirmation"
                type="password"
                placeholder="Confirmar contraseña"
                class="w-full mb-3 p-2 border rounded"
            />
            <label class="mb-3 block">
                <input type="checkbox" v-model="form.activo" class="mr-2" />
                ¿Activo?
            </label>

            <button
                type="submit"
                class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700"
            >
                Registrar
            </button>

            <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
            <p v-if="success" class="text-green-500 mt-2">{{ success }}</p>
        </form>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../axios";

const router = useRouter();

const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    activo: true,
});

const error = ref(null);
const success = ref(null);

const register = async () => {
    error.value = null;
    success.value = null;
    try {
        await api.post("/api/register", form.value);
        success.value = "Registro exitoso. Redirigiendo al login...";
        setTimeout(() => {
            router.push({ name: "login" });
        }, 2000);
    } catch (e) {
        error.value = e.response?.data?.message || "Ocurrió un error";
    }
};
</script>
