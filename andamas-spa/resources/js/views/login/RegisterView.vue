<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-6 rounded shadow w-full max-w-sm">
            <h2 class="text-2xl font-bold mb-4 text-center">Crear cuenta</h2>
            <form @submit.prevent="handleRegister" class="space-y-4">
                <input
                    v-model="name"
                    type="text"
                    placeholder="Nombre"
                    class="w-full border p-2 rounded"
                />
                <input
                    v-model="email"
                    type="email"
                    placeholder="Correo"
                    class="w-full border p-2 rounded"
                />
                <input
                    v-model="password"
                    type="password"
                    placeholder="Contraseña"
                    class="w-full border p-2 rounded"
                />
                <input
                    v-model="password_confirmation"
                    type="password"
                    placeholder="Confirmar contraseña"
                    class="w-full border p-2 rounded"
                />
                <button
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700"
                >
                    Registrarse
                </button>
            </form>
            <p class="text-sm mt-3 text-center">
                ¿Ya tienes cuenta?
                <router-link class="text-blue-600" to="/"
                    >Inicia sesión</router-link
                >
            </p>
            <div v-if="error" class="mt-4 bg-red-100 text-red-600 p-2 rounded">
                {{ error }}
            </div>
            <div
                v-if="success"
                class="mt-4 bg-green-100 text-green-700 p-2 rounded text-center"
            >
                {{ success }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            error: null,
            success: null,
        };
    },
    methods: {
        async handleRegister() {
            this.error = null;
            this.success = null;
            try {
                await fetch("/sanctum/csrf-cookie", { credentials: "include" });

                const res = await fetch("/api/register", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    credentials: "include",
                    body: JSON.stringify({
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                    }),
                });

                const data = await res.json();

                if (!res.ok) {
                    Swal.fire({
                        icon: "error",
                        title: "Registro fallido",
                        text:
                            data.message ||
                            "Ocurrió un error durante el registro",
                    });
                    return;
                }

                // Éxito
                Swal.fire({
                    icon: "success",
                    title: "Cuenta creada",
                    text: data.message || "Cuenta registrada correctamente",
                    timer: 4000,
                    showConfirmButton: false,
                });

                setTimeout(() => this.$router.push("/"), 2000);
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Error de conexión",
                    text: "No se pudo contactar al servidor",
                });
            }
        },
    },
};
</script>
