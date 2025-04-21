<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const email = ref('');
const password = ref('');
const error = ref(null);

const handleLogin = async () => {
  try {
    // Usamos el store para manejar el login
    await authStore.login(email.value, password.value);
    
    // Redirigir después de login exitoso
    router.push('/');
    
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al iniciar sesión';
    console.error('Error completo:', {
      status: err.response?.status,
      data: err.response?.data,
      message: err.message
    });
  }
};


</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <form @submit.prevent="handleLogin" class="bg-white p-8 rounded shadow-md w-96">
      <h2 class="text-2xl font-bold mb-4">Iniciar Sesión</h2>
      
      <input
        v-model="email"
        type="email"
        placeholder="Email"
        class="w-full mb-4 p-2 border rounded"
        required
      />
      
      <input
        v-model="password"
        type="password"
        placeholder="Contraseña"
        class="w-full mb-4 p-2 border rounded"
        required
      />
      
      <button
        type="submit"
        class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 cursor-pointer"
      >
        Entrar
      </button>
      
      <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
    </form>
  </div>
</template>