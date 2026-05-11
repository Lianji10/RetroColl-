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
  },
  {
    path: '/editar-perfil',
    name: 'EditarPerfil',
    component: () => import('../views/VistaEditarPerfil.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/VistaDashboard.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: () => import('../views/VistaCheckout.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/ayuda',
    name: 'Ayuda',
    component: () => import('../views/VistaAyuda.vue')
  },
  // 404 - debe ser la última ruta
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('../views/Vista404.vue')
  }
]

import { useAuthStore } from '../stores/authStore'

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0, behavior: 'smooth' }),
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Guardar ruta destino para redirigir tras el login
    next({ path: '/login', query: { redirect: to.fullPath } })
  } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/')
  } else if ((to.path === '/login' || to.path === '/registro') && authStore.isAuthenticated) {
    next('/perfil')
  } else {
    next()
  }
})

export default router
