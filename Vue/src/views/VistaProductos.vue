<template>
  <section id="productos" class="contenedor animate-in fade-in slide-in-from-left-4 duration-500">
    <div class="mb-10 text-center md:text-left">
      <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Todos los juegos</h2>
      <div class="h-1 w-20 bg-retro-amarillo mt-2 mx-auto md:mx-0"></div>
    </div>

    <!-- Filtros -->
    <div
      class="productos-controles bg-retro-gris border border-stone-200 rounded-xl p-6 mb-10 shadow-sm">
      <div class="flex flex-col sm:flex-row gap-6">
        <div class="flex-1">
          <label class="block text-xs font-bold text-stone-500 uppercase mb-2 font-mono">Filtrar por
            Categoría</label>
          <select v-model="filtroCategoria"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer appearance-none">
            <option value="">Todas las categorías</option>
            <option v-for="cat in categorias" :key="cat" :value="cat.toLowerCase()">
              {{ cat }}
            </option>
          </select>
        </div>

        <div class="flex-1">
          <label class="block text-xs font-bold text-stone-500 uppercase mb-2 font-mono">Rango de
            Precio</label>
          <select v-model="filtroPrecio"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer appearance-none">
            <option value="">Cualquier precio</option>
            <option value="0-30">0€ - 30€</option>
            <option value="30-60">30€ - 60€</option>
            <option value="60-100">60€ - 100€</option>
            <option value="100-300">100€ - 300€</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Grid -->
    <div v-if="productosFiltrados.length > 0" class="products-grid">
      <TarjetaProducto v-for="producto in productosFiltrados" :key="producto.id" :producto="producto"
        @añadir="(p) => emits('añadir-al-carrito', p)" />
    </div>

    <!-- Empty State -->
    <div v-else class="bg-retro-gris border-2 border-dashed border-stone-200 rounded-2xl p-20 text-center">
      <div class="text-stone-300 text-6xl mb-4 grayscale opacity-40">🔍</div>
      <h3 class="text-xl font-bold text-stone-400 uppercase tracking-tight">No se encontraron productos</h3>
      <p class="text-stone-400 mt-2 text-sm">Prueba con otros filtros, coleccionista.</p>
      <button @click="limpiarFiltros"
        class="mt-8 text-retro-amarillo hover:underline font-bold uppercase text-xs bg-blue-50 px-6 py-2 rounded-full transition-all">
        Limpiar filtros
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TarjetaProducto from '../components/TarjetaProducto.vue'
import api from '../services/api'

const emits = defineEmits(['añadir-al-carrito'])

const categorias = ref([])
const productos = ref([])

const filtroCategoria = ref('')
const filtroPrecio = ref('')

const cargarDatos = async () => {
  try {
    const res = await api.get('/productos')
    productos.value = res.data
    // Extraer categorías únicas de la respuesta
    const uniqueCats = new Set(productos.value.map(p => p.categoria?.nombre || p.categoria?.nombre_categoria).filter(Boolean))
    categorias.value = Array.from(uniqueCats)
  } catch (err) {
    console.error('Error cargando catálogo desde la API:', err)
  }
}

onMounted(() => {
  cargarDatos()
})

const productosFiltrados = computed(() => {
  return productos.value.filter(p => {
    const nombreCat = p.categoria?.nombre || p.categoria?.nombre_categoria
    const matchCat = !filtroCategoria.value || (nombreCat && nombreCat.toLowerCase() === filtroCategoria.value)

    let matchPrecio = true
    if (filtroPrecio.value) {
      const [min, max] = filtroPrecio.value.split('-').map(Number)
      matchPrecio = p.precio >= min && p.precio <= max
    }

    return matchCat && matchPrecio
  })
})

const limpiarFiltros = () => {
  filtroCategoria.value = ''
  filtroPrecio.value = ''
}
</script>
