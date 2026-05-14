<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { useCartStore } from '../stores/cartStore'
import api from '../services/api'

const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'

const props = defineProps({
  producto: {
    type: Object,
    required: true
  }
})

const emits = defineEmits(['productoEliminado'])

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const añadirAlCarrito = () => {
  cartStore.addToCart(props.producto)
}

const esMio = computed(() => {
  return authStore.isAuthenticated && authStore.user?.id_usuario === props.producto.id_vendedor
})

const verDetalle = () => {
  router.push('/productos/' + (props.producto.id_producto || props.producto.id))
}

const editarProducto = () => {
  router.push('/venta?edit=' + (props.producto.id_producto || props.producto.id))
}

const eliminarProducto = async () => {
  if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
    try {
      const id = props.producto.id_producto || props.producto.id
      await api.delete(`/productos/${id}`)
      emits('productoEliminado', id)
    } catch (e) {
      console.error('Error al eliminar el producto', e)
      alert('No se pudo eliminar el producto. Inténtalo de nuevo.')
    }
  }
}
</script>

<template>
  <div @click="verDetalle"
    class="bg-retro-gris border-2 border-stone-200 rounded-xl p-5 hover:border-retro-amarillo transition-all group overflow-hidden relative shadow-md cursor-pointer">
    <div class="absolute top-2 right-2 flex flex-col gap-1 items-end z-10">
      <span
        class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-green-300 uppercase">
        {{ producto.estado }}
      </span>
    </div>

    <div v-if="producto.imagen"
      class="mb-4 aspect-square bg-stone-100 rounded-lg overflow-hidden border border-stone-200">
      <img :src="`${apiUrl}${producto.imagen}`" :alt="producto.titulo" class="w-full h-full object-cover">
    </div>
    <div v-else
      class="mb-4 aspect-square bg-stone-100 rounded-lg flex items-center justify-center text-stone-400 text-xs text-center p-4 italic border border-stone-200">
      Sin imagen
    </div>

    <h4 class="text-retro-texto font-bold mb-1 truncate">{{ producto.titulo }}</h4>
    <p class="text-retro-amarillo font-bold text-xl mb-3">{{ producto.precio }}€</p>

    <div class="flex items-center justify-between mt-auto">
      <div v-if="esMio" class="flex gap-2">
        <span
          class="bg-red-50 text-[#B22222] text-[10px] px-2 py-1.5 rounded-md font-bold uppercase tracking-widest border border-red-200"
          title="Este es tu artículo">
          Tuyo
        </span>
        <div class="flex gap-1" v-if="producto.estado !== 'Vendido'">
          <button @click.stop="editarProducto"
            class="bg-stone-200 text-stone-600 p-1.5 rounded-lg hover:bg-retro-amarillo hover:text-white transition-colors"
            title="Editar producto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
          </button>
          <button @click.stop="eliminarProducto"
            class="bg-stone-200 text-red-500 p-1.5 rounded-lg hover:bg-red-500 hover:text-white transition-colors"
            title="Eliminar producto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
      <button v-else-if="producto.estado !== 'Vendido'" @click.stop="añadirAlCarrito"
        class="bg-retro-amarillo text-white p-2 rounded-lg hover:bg-[#8B1111] transition-colors shadow-lg shadow-[#B22222]/20 transform active:scale-95"
        title="Añadir al carrito">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path
            d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 100-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
        </svg>
      </button>
    </div>
  </div>
</template>
