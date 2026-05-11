<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import { useRouter } from 'vue-router'
import api, { getValoracionesUsuario } from '../services/api'
import TarjetaProducto from '../components/TarjetaProducto.vue'
import ModalValoracion from '../components/ModalValoracion.vue'

const authStore = useAuthStore()
const router = useRouter()
const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'

const misProductos = ref([])
const misVendidos = ref([])
const misCompras = ref([])
const cargando = ref(true)
const cargandoCompras = ref(false)
const tabActivo = ref('venta')

// Valoraciones
const valoraciones = ref([])
const cargandoValoraciones = ref(false)
const mostrarModalValoracion = ref(false)
const receptorSeleccionado = ref(null)

const formatFecha = (fecha) => {
  if (!fecha) return ''
  return new Date(fecha).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  try {
    const response = await api.get('/productos')
    const userProducts = response.data.filter(
      p => p.id_vendedor === authStore.user?.id_usuario
    )
    misProductos.value = userProducts.filter(p => p.estado !== 'Vendido')
    misVendidos.value = userProducts.filter(p => p.estado === 'Vendido')
  } catch (error) {
    console.error('Error cargando los productos del usuario', error)
  } finally {
    cargando.value = false
  }
  // Cargar historial de compras
  cargandoCompras.value = true
  try {
    const res = await api.get('/compras/mis-compras')
    misCompras.value = res.data
  } catch (e) {
    console.error('Error cargando compras', e)
  } finally {
    cargandoCompras.value = false
  }

  // Cargar mis valoraciones (recibidas)
  await cargarValoraciones()
})

const cargarValoraciones = async () => {
  if (!authStore.user?.id_usuario) return
  cargandoValoraciones.value = true
  try {
    const res = await getValoracionesUsuario(authStore.user.id_usuario)
    valoraciones.value = res.data
  } catch (e) {
    console.error('Error cargando valoraciones', e)
  } finally {
    cargandoValoraciones.value = false
  }
}

const abrirModalValorar = (vendedor) => {
  receptorSeleccionado.value = vendedor
  mostrarModalValoracion.value = true
}

const handleProductoEliminado = (id) => {
  misProductos.value = misProductos.value.filter(p => (p.id_producto || p.id) !== id)
}

const cerrarSesion = async () => {
  await authStore.logout()
  // Tras la limpieza, llevamos al user al inicio o login
  router.push('/login')
}
</script>

<template>
  <section id="perfil" class="contenedor animate-in fade-in duration-500">
    <div class="mb-10">
      <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Mi Perfil</h2>
      <div class="h-1 w-20 bg-retro-amarillo mt-2"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
      <!-- Datos del Perfil (Izquierda en desktop) -->
      <div class="lg:col-span-1">
        <div
          class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-10 text-center shadow-lg relative overflow-hidden">
          <div
            class="w-24 h-24 bg-retro-amarillo mx-auto rounded-2xl flex items-center justify-center text-white font-bold text-3xl shadow-lg shadow-retro-amarillo/20 mb-6 uppercase">
            {{ authStore.user?.nombre ? authStore.user.nombre.substring(0, 2) : 'VIP' }}
          </div>

          <h3 class="text-3xl font-bold text-retro-texto mb-2 tracking-tight">{{ authStore.user?.nombre ||
            'Coleccionista' }}</h3>
          <p class="text-retro-amarillo text-sm font-medium font-mono mb-6">{{ authStore.user?.email || 'cargando...' }}
          </p>

          <div
            class="inline-block bg-stone-100 border border-stone-200 rounded-full px-5 py-2 text-xs text-stone-500 font-bold uppercase tracking-widest mb-8">
            Miembro Verificado
          </div>

          <div class="flex flex-col gap-4">
            <router-link to="/editar-perfil"
              class="btn-retro-secondary w-full text-center py-4 text-sm font-bold tracking-wide uppercase">
              Editar Perfil
            </router-link>
            <router-link to="/venta"
              class="btn-retro-primary w-full text-center block py-4 text-sm font-bold tracking-wide uppercase">
              Publicar un artículo
            </router-link>
          </div>

          <div class="mt-10 pt-6 border-t border-stone-200">
            <button @click="cerrarSesion"
              class="w-full flex items-center justify-center gap-2 text-red-500 hover:text-red-400 font-bold uppercase text-xs tracking-widest transition-colors group">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:-translate-x-1 transition-transform"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Cerrar Sesión
            </button>
          </div>
        </div>
      </div>

      <!-- Mis Artículos / Mis Compras -->
      <div class="lg:col-span-2">
        <!-- Tabs -->
        <div class="flex gap-2 mb-6 border-b-2 border-stone-200">
          <button @click="tabActivo = 'venta'"
            :class="tabActivo === 'venta' ? 'border-b-2 border-retro-amarillo text-retro-amarillo -mb-[2px]' : 'text-stone-500 hover:text-retro-amarillo'"
            class="font-bold uppercase text-xs tracking-widest px-5 py-3 transition-colors">
            En venta
          </button>
          <button @click="tabActivo = 'vendidos'"
            :class="tabActivo === 'vendidos' ? 'border-b-2 border-retro-amarillo text-retro-amarillo -mb-[2px]' : 'text-stone-500 hover:text-retro-amarillo'"
            class="font-bold uppercase text-xs tracking-widest px-5 py-3 transition-colors">
            Vendidos
          </button>
          <button @click="tabActivo = 'compras'"
            :class="tabActivo === 'compras' ? 'border-b-2 border-retro-amarillo text-retro-amarillo -mb-[2px]' : 'text-stone-500 hover:text-retro-amarillo'"
            class="font-bold uppercase text-xs tracking-widest px-5 py-3 transition-colors">
            Mis compras
          </button>
          <button @click="tabActivo = 'valoraciones'"
            :class="tabActivo === 'valoraciones' ? 'border-b-2 border-retro-amarillo text-retro-amarillo -mb-[2px]' : 'text-stone-500 hover:text-retro-amarillo'"
            class="font-bold uppercase text-xs tracking-widest px-5 py-3 transition-colors">
            Valoraciones
          </button>
        </div>

        <!-- Tab: Artículos en venta -->
        <div v-if="tabActivo === 'venta'">
          <div v-if="cargando" class="flex justify-center py-10">
            <div class="w-10 h-10 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin"></div>
          </div>
          <div v-else-if="misProductos.length === 0"
            class="bg-stone-50 border border-stone-200 rounded-xl p-10 text-center flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-stone-300 mb-4" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="font-bold uppercase tracking-widest text-sm text-stone-500 mb-6">No tienes artículos en venta</p>
            <router-link to="/venta" class="btn-retro-primary py-3 px-6 text-sm font-bold uppercase tracking-wide">
              Publicar tu primer juego
            </router-link>
          </div>
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <TarjetaProducto v-for="producto in misProductos" :key="producto.id_producto" :producto="producto" 
              @productoEliminado="handleProductoEliminado" />
          </div>
        </div>

        <!-- Tab: Artículos vendidos -->
        <div v-else-if="tabActivo === 'vendidos'">
          <div v-if="cargando" class="flex justify-center py-10">
            <div class="w-10 h-10 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin"></div>
          </div>
          <div v-else-if="misVendidos.length === 0"
            class="bg-stone-50 border border-stone-200 rounded-xl p-10 text-center flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-stone-300 mb-4" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-bold uppercase tracking-widest text-sm text-stone-500">No has vendido ningún artículo aún</p>
          </div>
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <TarjetaProducto v-for="producto in misVendidos" :key="producto.id_producto" :producto="producto" />
          </div>
        </div>

        <!-- Tab: Historial de compras -->
        <div v-else-if="tabActivo === 'compras'">
          <div v-if="cargandoCompras" class="flex justify-center py-10">
            <div class="w-10 h-10 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin"></div>
          </div>
          <div v-else-if="misCompras.length === 0"
            class="bg-stone-50 border border-stone-200 rounded-xl p-10 text-center flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-stone-300 mb-4" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="font-bold uppercase tracking-widest text-sm text-stone-500">Todavía no has comprado nada</p>
          </div>
          <div v-else class="space-y-4">
            <div v-for="compra in misCompras" :key="compra.id_compra"
              class="bg-retro-gris border-2 border-stone-200 rounded-xl p-5 flex items-center gap-5 hover:border-retro-amarillo transition-all shadow-sm">
              <!-- Imagen -->
              <div v-if="compra.producto?.imagen"
                class="w-20 h-20 rounded-lg overflow-hidden border border-stone-200 shrink-0">
                <img :src="`${apiUrl}${compra.producto.imagen}`" :alt="compra.producto?.titulo"
                  class="w-full h-full object-cover" />
              </div>
              <div v-else
                class="w-20 h-20 bg-stone-100 rounded-lg border border-stone-200 shrink-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-stone-300" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <!-- Info -->
              <div class="flex-1 min-w-0">
                <p class="font-bold text-retro-texto truncate">{{ compra.producto?.titulo || 'Producto eliminado' }}</p>
                <div class="flex gap-3 mt-1">
                  <span v-if="compra.producto?.categoria"
                    class="text-xs bg-stone-100 text-stone-600 px-2 py-0.5 rounded font-mono">{{
                      compra.producto.categoria.nombre }}</span>
                  <span v-if="compra.producto?.plataforma"
                    class="text-xs bg-retro-amarillo/10 text-retro-amarillo px-2 py-0.5 rounded font-mono">{{
                      compra.producto.plataforma.nombre }}</span>
                </div>
                <p class="text-xs text-stone-400 mt-1">{{ formatFecha(compra.fecha_compra) }}</p>
              </div>
              <!-- Precio -->
              <div class="flex flex-col items-end gap-2">
                <p class="text-xl font-bold text-retro-amarillo shrink-0">{{ compra.precio_final }}€</p>
                <button v-if="compra.producto?.id_vendedor !== authStore.user?.id_usuario"
                  @click="abrirModalValorar(compra.producto.vendedor)"
                  class="text-[10px] font-bold uppercase tracking-widest text-stone-400 hover:text-retro-amarillo transition-colors flex items-center gap-1 group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 group-hover:fill-current" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                  Valorar vendedor
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Valoraciones -->
        <div v-else-if="tabActivo === 'valoraciones'">
          <div v-if="cargandoValoraciones" class="flex justify-center py-10">
            <div class="w-10 h-10 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin"></div>
          </div>
          <div v-else-if="valoraciones.length === 0"
            class="bg-stone-50 border border-stone-200 rounded-xl p-10 text-center flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-stone-300 mb-4" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            <p class="font-bold uppercase tracking-widest text-sm text-stone-500">Todavía no has recibido ninguna
              valoración</p>
          </div>
          <div v-else class="space-y-4">
            <div v-for="val in valoraciones" :key="val.id_valoracion"
              class="bg-retro-gris border border-stone-200 rounded-xl p-5">
              <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 bg-retro-amarillo/10 rounded flex items-center justify-center text-retro-amarillo font-bold text-xs">
                    {{ val.emisor?.nombre?.substring(0, 1) || 'U' }}
                  </div>
                  <div>
                    <p class="font-bold text-retro-texto text-sm">{{ val.emisor?.nombre || 'Usuario' }}</p>
                    <p class="text-[9px] text-stone-400 font-mono uppercase">{{ formatFecha(val.fecha) }}</p>
                  </div>
                </div>
                <div class="flex gap-0.5">
                  <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg"
                    :class="i <= val.puntuacion ? 'text-retro-amarillo fill-current' : 'text-stone-200 fill-none'"
                    class="h-3.5 w-3.5" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                </div>
              </div>
              <p class="text-sm text-stone-600 italic">"{{ val.comentario || 'Sin comentario' }}"</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modales -->
  <ModalValoracion v-if="mostrarModalValoracion" :receptorId="receptorSeleccionado?.id_usuario"
    :receptorNombre="receptorSeleccionado?.nombre" @cerrar="mostrarModalValoracion = false"
    @completado="cargarValoraciones" />
</template>
