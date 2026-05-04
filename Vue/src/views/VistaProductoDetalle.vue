<template>
  <section id="producto-detalle" class="contenedor animate-in fade-in slide-in-from-bottom-6 duration-500 py-12">
    <!-- Botón Volver -->
    <div class="mb-8">
      <button @click="$router.back()" class="flex items-center gap-2 text-stone-500 hover:text-retro-amarillo font-bold uppercase text-xs tracking-widest transition-colors group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver
      </button>
    </div>

    <!-- Cargando -->
    <div v-if="cargando" class="flex flex-col items-center justify-center py-20">
      <div class="w-16 h-16 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin mb-4"></div>
      <p class="text-stone-500 font-bold uppercase tracking-widest text-sm">Cargando producto...</p>
    </div>

    <!-- Error -->
    <div v-else-if="errorMsg" class="bg-red-50 border border-red-300 text-red-700 p-6 rounded-xl text-center max-w-lg mx-auto">
      <p class="font-bold mb-2">⚠️ Error</p>
      <p class="text-sm">{{ errorMsg }}</p>
      <button @click="$router.push('/productos')" class="mt-4 text-xs font-bold uppercase tracking-widest hover:underline">Ir a la tienda</button>
    </div>

    <!-- Contenido -->
    <div v-else-if="producto" class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-6 md:p-10 shadow-lg">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        
        <!-- Columna Izquierda: Imagen -->
        <div class="flex flex-col gap-4">
          <div v-if="producto.imagen" class="w-full aspect-square bg-stone-100 rounded-xl overflow-hidden border-2 border-stone-200 shadow-inner">
            <img :src="`http://localhost:8000${producto.imagen}`" :alt="producto.titulo" class="w-full h-full object-cover">
          </div>
          <div v-else class="w-full aspect-square bg-stone-100 rounded-xl flex items-center justify-center text-stone-400 text-sm text-center p-4 italic border-2 border-stone-200 shadow-inner">
            <div class="flex flex-col items-center gap-2">
              <span class="text-4xl">📸</span>
              <span>Sin imagen disponible</span>
            </div>
          </div>
          
          <div class="flex justify-between items-center px-2">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Estado de conservación</span>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-300 uppercase shadow-sm">
              {{ producto.estado }}
            </span>
          </div>
        </div>

        <!-- Columna Derecha: Detalles -->
        <div class="flex flex-col">
          <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
              <span class="bg-stone-100 text-stone-600 border border-stone-200 text-xs px-3 py-1 rounded-md uppercase font-bold tracking-wider">
                {{ producto.categoria?.nombre || 'General' }}
              </span>
              <span class="bg-retro-amarillo/10 text-retro-amarillo border border-retro-amarillo/20 text-xs px-3 py-1 rounded-md uppercase font-bold tracking-wider">
                {{ producto.plataforma?.nombre || 'General' }}
              </span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-retro-texto tracking-tight mb-4 leading-tight">{{ producto.titulo }}</h1>
            <p class="text-4xl font-bold text-retro-amarillo drop-shadow-sm mb-6">{{ producto.precio }}€</p>
          </div>

          <div class="prose prose-stone prose-sm md:prose-base text-stone-600 mb-8 border-y border-stone-200 py-6">
            <h3 class="text-sm font-bold text-retro-texto uppercase tracking-widest mb-3">Descripción</h3>
            <p class="whitespace-pre-line">{{ producto.descripcion || 'Sin descripción detallada.' }}</p>
          </div>

          <div class="bg-stone-50 border border-stone-200 rounded-xl p-5 mb-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-retro-amarillo rounded-full flex items-center justify-center text-white font-bold text-xl uppercase shadow-md shrink-0">
              {{ producto.vendedor?.nombre ? producto.vendedor.nombre.substring(0, 2) : 'US' }}
            </div>
            <div>
              <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-1">Vendedor</p>
              <p class="text-retro-texto font-bold">{{ producto.vendedor?.nombre || 'Usuario Retro' }}</p>
            </div>
          </div>

          <div class="mt-auto pt-4">
            <div v-if="esMio" class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-xl text-center font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
              Este artículo es tuyo
            </div>
            <button v-else @click="añadirAlCarrito" class="btn-retro-primary w-full py-5 text-lg uppercase font-bold flex items-center justify-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Añadir al carrito
            </button>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { useCartStore } from '../stores/cartStore'
import api from '../services/api'

const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()

const producto = ref(null)
const cargando = ref(true)
const errorMsg = ref('')

const esMio = computed(() => {
  return authStore.isAuthenticated && authStore.user?.id_usuario === producto.value?.id_vendedor
})

const cargarProducto = async () => {
  cargando.value = true
  errorMsg.value = ''
  try {
    const response = await api.get(`/productos/${route.params.id}`)
    producto.value = response.data
  } catch (err) {
    if (err.response?.status === 404) {
      errorMsg.value = 'El producto no existe o ha sido eliminado.'
    } else {
      errorMsg.value = 'Error de conexión. Inténtalo más tarde.'
    }
  } finally {
    cargando.value = false
  }
}

const añadirAlCarrito = () => {
  if (producto.value) {
    cartStore.addItem({
      id: producto.value.id_producto,
      titulo: producto.value.titulo,
      precio: producto.value.precio,
      imagen: producto.value.imagen
    })
    alert('Añadido al carrito con éxito')
  }
}

onMounted(() => {
  cargarProducto()
})
</script>
