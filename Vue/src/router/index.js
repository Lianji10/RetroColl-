import { createRouter, createWebHistory } from 'vue-router'
import VistaInicio from '../views/VistaInicio.vue'

const routes = [
  {
    path: '/',
    name: 'Inicio',
    component: VistaInicio
  },
  {
    path: '/categorias',
    name: 'Categorias',
    component: () => import('../views/VistaCategorias.vue')
  },
  {
    path: '/productos',
    name: 'Productos',
    component: () => import('../views/VistaProductos.vue')
  },
  {
    path: '/productos/:id',
    name: 'ProductoDetalle',
    component: () => import('../views/VistaProductoDetalle.vue')
  },
  {
    path: '/venta',
    name: 'Venta',
    component: () => import('../views/VistaVenta.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/VistaLogin.vue')
  },
  {
    path: '/registro',
    name: 'Registro',
    component: () => import('../views/VistaRegistro.vue')
  },
  {
    path: '/perfil',
    name: 'Perfil',
    component: () => import('../views/VistaPerfil.vue'),
    meta: { requiresAuth: true }
  }
]

import { useAuthStore } from '../stores/authStore'

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if ((to.path === '/login' || to.path === '/registro') && authStore.isAuthenticated) {
    next('/perfil')
  } else {
    next()
  }
})

export default router
