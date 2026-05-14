<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import TarjetaProducto from '../components/TarjetaProducto.vue'
import api from '../services/api'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()

const route = useRoute()

const categorias = ref([])
const plataformas = ref([])
const productos = ref([])

const filtroBusqueda = ref('')
const filtroCategoria = ref('')
const filtroPrecio = ref('')
const filtroPlataforma = ref('')
const ordenar = ref('reciente')

const cargarDatos = async () => {
  try {
    const [resProd, resPlat] = await Promise.all([
      api.get('/productos'),
      api.get('/plataformas')
    ])
    productos.value = resProd.data
    const uniqueCats = new Set(resProd.data.map(p => p.categoria?.nombre).filter(Boolean))
    categorias.value = Array.from(uniqueCats)
    plataformas.value = resPlat.data.map(p => p.nombre)
  } catch (err) {
    console.error('Error cargando catálogo desde la API:', err)
  }
}

onMounted(async () => {
  await cargarDatos()
  // Leer el query param ?categoria= y preseleccionar el filtro
  if (route.query.categoria) {
    filtroCategoria.value = String(route.query.categoria).toLowerCase()
  }
  // Leer el query param ?busqueda= y preseleccionar el filtro
  if (route.query.busqueda) {
    filtroBusqueda.value = String(route.query.busqueda)
  }
})

// Reaccionar si el query param cambia (ej: navegando desde distintas categorías)
watch(() => route.query.categoria, (val) => {
  filtroCategoria.value = val ? String(val).toLowerCase() : ''
})

watch(() => route.query.busqueda, (val) => {
  filtroBusqueda.value = val ? String(val) : ''
})

const productosFiltrados = computed(() => {
  let lista = productos.value.filter(p => {
    const matchEstado = p.estado !== 'Vendido'
    const nombreCat = p.categoria?.nombre
    const matchCat = !filtroCategoria.value || (nombreCat && nombreCat.toLowerCase() === filtroCategoria.value)
    const matchNombre = !filtroBusqueda.value || p.titulo?.toLowerCase().includes(filtroBusqueda.value.toLowerCase())
    const matchPlat = !filtroPlataforma.value || (p.plataforma?.nombre?.toLowerCase() === filtroPlataforma.value)
    
    // Filtro: No mostrar productos propios del usuario logueado
    const noEsPropio = !authStore.isAuthenticated || p.id_vendedor !== authStore.user?.id_usuario
    
    let matchPrecio = true
    if (filtroPrecio.value) {
      const [min, max] = filtroPrecio.value.split('-').map(Number)
      matchPrecio = p.precio >= min && p.precio <= max
    }
    return matchEstado && matchCat && matchNombre && matchPrecio && matchPlat && noEsPropio
  })
  // Ordenar
  if (ordenar.value === 'precio_asc') lista = [...lista].sort((a, b) => a.precio - b.precio)
  else if (ordenar.value === 'precio_desc') lista = [...lista].sort((a, b) => b.precio - a.precio)
  else if (ordenar.value === 'nombre_asc') lista = [...lista].sort((a, b) => a.titulo.localeCompare(b.titulo))
  return lista
})

const limpiarFiltros = () => {
  filtroBusqueda.value = ''
  filtroCategoria.value = ''
  filtroPrecio.value = ''
  filtroPlataforma.value = ''
  ordenar.value = 'reciente'
}
</script>

<template>
  <section id="productos" class="contenedor animate-in fade-in slide-in-from-left-4 duration-500">
    <div class="mb-10 text-center md:text-left">
      <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Todos los juegos</h2>
      <div class="h-1 w-20 bg-retro-amarillo mt-2 mx-auto md:mx-0"></div>
    </div>

    <!-- Filtros -->
    <div class="productos-controles bg-retro-gris border border-stone-200 rounded-xl p-6 mb-10 shadow-sm">
      <div class="flex flex-col sm:flex-row gap-4">

        <!-- Buscador por nombre -->
        <div class="flex-[2]">
          <label class="block text-xs font-bold text-stone-500 uppercase mb-2 font-mono">Buscar por nombre</label>
          <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input v-model="filtroBusqueda" type="text" placeholder="Ej: Mario, Final Fantasy..."
              class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg pl-9 pr-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all" />
            <button v-if="filtroBusqueda" @click="filtroBusqueda = ''"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-300 hover:text-stone-500 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
        </div>

        <!-- Categoría -->
        <div class="flex-1">
          <label class="block text-xs font-bold text-stone-500 uppercase mb-2 font-mono">Categoría</label>
          <select v-model="filtroCategoria"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer appearance-none">
            <option value="">Todas las categorías</option>
            <option v-for="cat in categorias" :key="cat" :value="cat.toLowerCase()">
              {{ cat }}
            </option>
          </select>
        </div>

        <!-- Precio -->
        <div class="flex-1">
          <label class="block text-xs font-bold text-stone-500 uppercase mb-2 font-mono">Precio</label>
          <select v-model="filtroPrecio"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer appearance-none">
            <option value="">Cualquier precio</option>
            <option value="0-30">0€ - 30€</option>
            <option value="30-60">30€ - 60€</option>
            <option value="60-100">60€ - 100€</option>
            <option value="100-300">100€ - 300€</option>
          </select>
        </div>

        <!-- Plataforma -->
        <div class="flex-1">
          <label class="block text-xs font-bold text-stone-500 uppercase mb-2 font-mono">Plataforma</label>
          <select v-model="filtroPlataforma"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer appearance-none">
            <option value="">Todas las plataformas</option>
            <option v-for="plat in plataformas" :key="plat" :value="plat.toLowerCase()">{{ plat }}</option>
          </select>
        </div>

        <!-- Ordenar -->
        <div class="flex-1">
          <label class="block text-xs font-bold text-stone-500 uppercase mb-2 font-mono">Ordenar por</label>
          <select v-model="ordenar"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer appearance-none">
            <option value="reciente">Más recientes</option>
            <option value="precio_asc">Precio: menor a mayor</option>
            <option value="precio_desc">Precio: mayor a menor</option>
            <option value="nombre_asc">Nombre A-Z</option>
          </select>
        </div>
      </div>

      <!-- Chips de filtros activos -->
      <div v-if="filtroCategoria || filtroPrecio || filtroBusqueda" class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-stone-200">
        <span v-if="filtroCategoria" class="inline-flex items-center gap-1 bg-retro-amarillo/10 text-retro-amarillo border border-retro-amarillo/30 text-xs font-bold px-3 py-1 rounded-full">
          {{ filtroCategoria }}
          <button @click="filtroCategoria = ''" class="hover:text-[#8B1111] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </span>
        <span v-if="filtroPrecio" class="inline-flex items-center gap-1 bg-retro-amarillo/10 text-retro-amarillo border border-retro-amarillo/30 text-xs font-bold px-3 py-1 rounded-full">
          {{ filtroPrecio }}€
          <button @click="filtroPrecio = ''" class="hover:text-[#8B1111] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </span>
        <button @click="limpiarFiltros" class="text-stone-400 hover:text-retro-amarillo text-xs font-bold uppercase tracking-wider transition-colors ml-auto">
          Limpiar todo
        </button>
      </div>
    </div>

    <!-- Contador de resultados -->
    <p v-if="productosFiltrados.length > 0" class="text-xs text-stone-400 font-mono mb-6">
      {{ productosFiltrados.length }} resultado{{ productosFiltrados.length !== 1 ? 's' : '' }}
    </p>

    <!-- Grid -->
    <div v-if="productosFiltrados.length > 0" class="products-grid">
      <TarjetaProducto v-for="producto in productosFiltrados" :key="producto.id_producto" :producto="producto" />
    </div>

    <!-- Empty State -->
    <div v-else class="bg-retro-gris border-2 border-dashed border-stone-200 rounded-2xl p-20 text-center">
      <div class="text-stone-300 mb-4 opacity-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
      </div>
      <h3 class="text-xl font-bold text-stone-400 uppercase tracking-tight">No se encontraron productos</h3>
      <p class="text-stone-400 mt-2 text-sm">Prueba con otros filtros, coleccionista.</p>
      <button @click="limpiarFiltros"
        class="mt-8 text-retro-amarillo hover:underline font-bold uppercase text-xs bg-red-50 px-6 py-2 rounded-full transition-all">
        Limpiar filtros
      </button>
    </div>
  </section>
</template>
