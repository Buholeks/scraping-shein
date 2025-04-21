// useAuthStore.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export const useAuthStore = defineStore('auth', () => {
    const router = useRouter();

    // Estados reactivos
    const user = ref(null);
    const token = ref(null);
    const idEmpresa = ref(null);
    const empresaNombre = ref('');
    const sucursales = ref([]); // Todas las sucursales del usuario
    const sucursalActual = ref(null); // Sucursal seleccionada actualmente

    // Computed properties
    const isAuthenticated = computed(() => !!token.value);
    const userInitials = computed(() =>
        user.value?.name
            ? user.value.name.split(' ').map(n => n[0]).join('').toUpperCase()
            : ''
    );
    const empresaInfo = computed(() => ({
        id: idEmpresa.value,
        nombre: empresaNombre.value
    }));
    const sucursalInfo = computed(() => ({
        id: sucursalActual.value?.id,
        nombre: sucursalActual.value?.nombre
    }));

    // Métodos
    async function login(email, password) {
        try {
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/api/login', { email, password });
            setAuthData(response.data);
            return true;
        } catch (error) {
            console.error('Login error:', error);
            resetAuth();
            throw error;
        }
    }

    async function logout() {
        try {
            await axios.post('/api/logout');
            resetAuth();
            router.push('/login');
        } catch (error) {
            console.error('Logout error:', error);
        }
    }

    async function cambiarSucursal(nuevoIdSucursal) {
        try {
            console.log('Buscando sucursal ID:', nuevoIdSucursal); // Debug
            console.log('En listado:', this.sucursales); // Debug

            const sucursal = sucursales.value.find(s => s.id === nuevoIdSucursal);
            if (!sucursal) {
                throw new Error(`Sucursal con ID ${nuevoIdSucursal} no encontrada en las sucursales disponibles`);
            }

            const response = await axios.post('/api/cambiar-sucursal', {
                sucursal_id: nuevoIdSucursal
            }, {
                headers: { Authorization: `Bearer ${this.token}` }
            });

            console.log('Respuesta del servidor:', response.data); // Debug

            // Actualiza la sucursal actual con los datos del servidor
            this.sucursalActual = response.data.sucursal || sucursal;

            // Actualiza localStorage
            if (this.user) {
                const userData = JSON.parse(localStorage.getItem('userData') || '{}');
                userData.sucursal_actual = this.sucursalActual;
                localStorage.setItem('userData', JSON.stringify(userData));
            }

            return response.data;
        } catch (error) {
            console.error('Error completo:', {
                message: error.message,
                response: error.response?.data,
                stack: error.stack
            });
            throw error;
        }
    }
    function setAuthData(authData) {
        token.value = authData.token;
        user.value = authData.user;
        idEmpresa.value = authData.user?.id_empresa || null;
        empresaNombre.value = authData.user?.empresa_nombre || '';

        function getSucursalId() {
            return sucursalActual.value?.id || null;
        }
        
        // Carga todas las sucursales
        sucursales.value = authData.user?.sucursales || [];

        // Establece sucursal actual (la que viene en sucursal_actual o primera de la lista)
        sucursalActual.value = authData.user?.sucursal_actual ||
            (sucursales.value.length > 0 ? sucursales.value[0] : null);

        // Guarda en localStorage
        localStorage.setItem('authToken', token.value);
        localStorage.setItem('userData', JSON.stringify({
            ...authData.user,
            sucursales: sucursales.value,
            sucursal_actual: sucursalActual.value
        }));

        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    }
    function resetAuth() {
        token.value = null;
        user.value = null;
        idEmpresa.value = null;
        empresaNombre.value = '';
        sucursales.value = [];
        sucursalActual.value = null;
        localStorage.removeItem('authToken');
        localStorage.removeItem('userData');
        delete axios.defaults.headers.common['Authorization'];
    }

    function initialize() {
        const storedToken = localStorage.getItem('authToken');
        const storedUser = localStorage.getItem('userData');

        if (storedToken && storedUser) {
            try {
                setAuthData({
                    token: storedToken,
                    user: JSON.parse(storedUser)
                });
                axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`;
            } catch (e) {
                console.error('Error al inicializar:', e);
                resetAuth();
            }
        }
    }


    return {
        // Estados
        user,
        token,
        idEmpresa,
        empresaNombre,
        sucursales,
        sucursalActual,

        // Computed
        isAuthenticated,
        userInitials,
        empresaInfo,
        sucursalInfo,

        // Métodos
        login,
        logout,
        cambiarSucursal,
        initialize,
        resetAuth,
        setAuthData
    };
});