<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import TarjetaProducto from '../components/TarjetaProducto.vue'
import api from '../services/api'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const router = useRouter()

const busqueda = ref('')
const productos = ref([])

const cargarDatos = async () => {
  try {
    const res = await api.get('/productos')
    productos.value = res.data
  } catch (error) {
    console.error('Error cargando destacados:', error)
  }
}

onMounted(() => {
  cargarDatos()
})

const productosDestacados = computed(() => {
  // Filtramos los vendidos, los que son del propio usuario y tomamos los últimos 4
  return productos.value.filter(p => {
    const matchEstado = p.estado !== 'Vendido'
    const noEsPropio = !authStore.isAuthenticated || p.id_vendedor !== authStore.user?.id_usuario
    return matchEstado && noEsPropio
  }).slice(0, 4)
})

const buscar = () => {
  const query = busqueda.value.trim()
  if (query) {
    router.push({ path: '/productos', query: { busqueda: query } })
  } else {
    router.push('/productos')
  }
}
</script>

<template>
  <section id="inicio" class="contenedor animate-in fade-in duration-700">
    <!-- Hero Section -->
    <div
      class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 md:p-16 mb-12 text-center relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-1 bg-retro-amarillo opacity-50"></div>

      <h2 class="text-4xl md:text-5xl font-bold text-retro-amarillo mb-4 tracking-tight uppercase">
        Compra y vende videojuegos retro
      </h2>
      <p class="text-stone-500 text-lg mb-8 max-w-2xl mx-auto">
        La mejor comunidad de coleccionistas para encontrar esas joyas del pasado.
      </p>
      <div class="flex flex-col sm:flex-row gap-3 max-w-xl mx-auto justify-center">
        <input type="text" v-model="busqueda" placeholder="¿Qué joya buscas hoy?..."
          @keyup.enter="buscar"
          class="flex-1 bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
        <button @click="buscar" class="btn-retro-primary px-8">Buscar</button>
      </div>
    </div>

    <!-- Destacados -->
    <div class="mb-16">
      <div class="flex justify-between items-end mb-8">
        <div>
          <h3 class="text-2xl font-bold text-retro-texto uppercase tracking-wider">Juegos destacados</h3>
          <div class="h-1 w-20 bg-retro-amarillo mt-1"></div>
        </div>
        <router-link to="/productos" class="text-retro-amarillo text-sm hover:underline">Ver todos los juegos
          →</router-link>
      </div>

      <div class="products-grid">
        <TarjetaProducto v-for="producto in productosDestacados" :key="producto.id" :producto="producto" />
      </div>
    </div>

    <!-- Features -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="bg-retro-gris border border-stone-200 p-8 rounded-xl text-center hover:bg-stone-50 transition-colors shadow-sm">
        <div class="text-retro-amarillo mb-4 flex justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-retro-texto mb-2 font-mono uppercase tracking-tight">Compra segura</h3>
        <p class="text-stone-500 text-sm">Todos los vendedores están verificados por nuestra comunidad.</p>
      </div>
      <div
        class="bg-retro-gris border border-stone-200 p-8 rounded-xl text-center hover:bg-stone-50 transition-colors shadow-sm">
        <div class="text-retro-amarillo mb-4 flex justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-retro-texto mb-2 font-mono uppercase tracking-tight">Envío rápido</h3>
        <p class="text-stone-500 text-sm">Recibe tus juegos en 24-48h con seguimiento real.</p>
      </div>
      <div
        class="bg-retro-gris border border-stone-200 p-8 rounded-xl text-center hover:bg-stone-50 transition-colors shadow-sm">
        <div class="text-retro-amarillo mb-4 flex justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-retro-texto mb-2 font-mono uppercase tracking-tight">Mejores precios</h3>
        <p class="text-stone-500 text-sm">Encuentra las mejores ofertas del mercado retro.</p>
      </div>
    </div>
  </section>
</template>
