import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from './stores/authStore'
import ProductosView from '@/views/clientes/ProductosView.vue'
import RegistrarVenta from '@/views/RegistrarVenta.vue'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/Login.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/Register.vue'),
    meta: { guestOnly: true }
  },

  {
    path: '/app', // Ruta base para el layout autenticado
    component: () => import('@/layout/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '', // /app/
        name: '/home',
        component: () => import('@/views/Home.vue')
      },
      {
        path: '/clientes', // /app/clientes
        name: 'clientes',
        component: () => import('@/views/clientes/Index.vue')
      },
      {
        path: '/shein', // /app/shein
        name: 'shein',
        component: () => import('@/views/shein/Buscar.vue')
      },
      {
        path: '/productos-entrega',
        name: 'productos.entrega',
        component: ProductosView,
      },

      {
        path: '/ventas',
        name: 'ventas.registrar',
        component: RegistrarVenta,
      }
    ]
  },
  // Redirección para rutas no encontradas
  {
    path: '/:pathMatch(.*)*',
    redirect: '/app' // o a '/login' según tu flujo
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Si la ruta requiere autenticación y no hay usuario
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next('/login')
  }

  // Si la ruta es solo para invitados y el usuario está autenticado
  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return next('/') // Redirige al dashboard
  }

  next()
})

export default router