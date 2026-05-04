<template>
  <section id="perfil" class="contenedor animate-in fade-in duration-500">
    <div class="mb-10">
      <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Mi Perfil</h2>
      <div class="h-1 w-20 bg-retro-amarillo mt-2"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
      <!-- Datos del Perfil (Izquierda en desktop) -->
      <div class="lg:col-span-1">
        <div class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-10 text-center shadow-lg relative overflow-hidden">
        <!-- Decoración -->
        <div class="absolute top-0 right-0 w-24 h-24 bg-retro-amarillo/10 -mr-12 -mt-12 rounded-full"></div>

        <div class="w-24 h-24 bg-retro-amarillo mx-auto rounded-2xl flex items-center justify-center text-white font-bold text-3xl shadow-lg shadow-retro-amarillo/20 mb-6 rotate-3 uppercase">
          {{ authStore.user?.nombre ? authStore.user.nombre.substring(0, 2) : 'VIP' }}
        </div>
        
        <h3 class="text-3xl font-bold text-retro-texto mb-2 tracking-tight">{{ authStore.user?.nombre || 'Coleccionista' }}</h3>
        <p class="text-retro-amarillo text-sm font-medium font-mono mb-6">{{ authStore.user?.email || 'cargando...' }}</p>

        <div class="inline-block bg-stone-100 border border-stone-200 rounded-full px-5 py-2 text-xs text-stone-500 font-bold uppercase tracking-widest mb-8">
          Miembro Verificado
        </div>

        <div class="flex flex-col gap-4">
          <button class="btn-retro-secondary w-full py-4 text-sm font-bold tracking-wide uppercase">
            Editar Perfil
          </button>
          <router-link to="/venta" class="btn-retro-primary w-full text-center block py-4 text-sm font-bold tracking-wide uppercase">
            Publicar un artículo
          </router-link>
        </div>

        <div class="mt-10 pt-6 border-t border-stone-200">
          <button @click="cerrarSesion" class="w-full flex items-center justify-center gap-2 text-red-500 hover:text-red-400 font-bold uppercase text-xs tracking-widest transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Cerrar Sesión
          </button>
        </div>
        </div>
      </div>

      <!-- Mis Artículos en Venta (Derecha en desktop) -->
      <div class="lg:col-span-2">
        <h3 class="text-2xl font-bold text-retro-texto uppercase tracking-tight mb-6 flex items-center gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          Mis Artículos en Venta
        </h3>

        <div v-if="cargando" class="flex justify-center py-10">
          <div class="w-10 h-10 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin"></div>
        </div>

        <div v-else-if="misProductos.length === 0" class="bg-stone-50 border border-stone-200 rounded-xl p-10 text-center flex flex-col items-center justify-center">
          <span class="text-4xl mb-4">👻</span>
          <p class="font-bold uppercase tracking-widest text-sm text-stone-500 mb-6">No tienes artículos en venta</p>
          <router-link to="/venta" class="btn-retro-primary py-3 px-6 text-sm font-bold uppercase tracking-wide">
            Publicar tu primer juego
          </router-link>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <TarjetaProducto 
            v-for="producto in misProductos" 
            :key="producto.id_producto" 
            :producto="producto" 
          />
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import { useRouter } from 'vue-router'
import api from '../services/api'
import TarjetaProducto from '../components/TarjetaProducto.vue'

const authStore = useAuthStore()
const router = useRouter()

const misProductos = ref([])
const cargando = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/productos')
    // Filtrar los productos del usuario
    misProductos.value = response.data.filter(
      p => p.id_vendedor === authStore.user?.id_usuario
    )
  } catch (error) {
    console.error('Error cargando los productos del usuario', error)
  } finally {
    cargando.value = false
  }
})

const cerrarSesion = async () => {
  await authStore.logout()
  // Tras la limpieza, llevamos al user al inicio o login
  router.push('/login')
}
</script>
