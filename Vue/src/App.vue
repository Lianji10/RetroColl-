<script setup>
import { ref } from 'vue'
import { RouterView, useRouter } from 'vue-router'
import Navegacion from './components/Navegacion.vue'
import PieDePagina from './components/PieDePagina.vue'
import ModalCarrito from './components/ModalCarrito.vue'
import { useCartStore } from './stores/cartStore'
import { useAuthStore } from './stores/authStore'

const router = useRouter()

const carritoAbierto = ref(false)
const cartStore = useCartStore()
const authStore = useAuthStore()

// Intentar recuperar el usuario si hay token pero no user (ej. tras f5)
authStore.fetchUser()

const toggleCarrito = () => {
  carritoAbierto.value = !carritoAbierto.value
}

const eliminarDelCarrito = (id) => {
  cartStore.removeFromCart(id)
}


const vaciarCarrito = () => {
  cartStore.clearCart()
}

const procesarCompra = () => {
  toggleCarrito()
  router.push('/checkout')
}
</script>

<template>
  <div class="min-h-screen bg-retro-oscuro text-retro-texto overflow-x-hidden relative">
    <Navegacion :cantidad-carrito="cartStore.totalItems" @toggleCarrito="toggleCarrito" />

    <main>
      <RouterView />
    </main>

    <PieDePagina />


    <ModalCarrito :abierto="carritoAbierto" :items="cartStore.items" :total="cartStore.totalPrice" @cerrar="toggleCarrito"
      @eliminar="eliminarDelCarrito" @vaciar="vaciarCarrito" @checkout="procesarCompra" />
  </div>
</template>
