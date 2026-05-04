<script setup>
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import Navegacion from './components/Navegacion.vue'
import PieDePagina from './components/PieDePagina.vue'
import ModalCarrito from './components/ModalCarrito.vue'
import { useCartStore } from './stores/cartStore'
import { useAuthStore } from './stores/authStore'

const carritoAbierto = ref(false)
const cartStore = useCartStore()
const authStore = useAuthStore()

// Intentar recuperar el usuario si hay token pero no user (ej. tras f5)
authStore.fetchUser()

const toggleCarrito = () => {
  carritoAbierto.value = !carritoAbierto.value
}

const añadirAlCarrito = (producto) => {
  cartStore.addToCart(producto)
}

const eliminarDelCarrito = (id) => {
  cartStore.removeFromCart(id)
}

const cambiarCantidad = (payload) => {
  const { id, delta } = payload
  const item = cartStore.items.find(item => item.id === id)
  if (item) {
    if (item.cantidad + delta <= 0) {
      cartStore.removeFromCart(id)
    } else {
      item.cantidad += delta
    }
  }
}

const vaciarCarrito = () => {
  cartStore.clearCart()
}

const procesarCompra = async () => {
  try {
    await cartStore.checkout()
    alert('¡Proceso de Checkout tramitado desde Vue hacia el Store!')
    toggleCarrito()
  } catch (err) {
    alert('Hubo un error en la compra.')
  }
}
</script>

<template>
  <div class="min-h-screen bg-retro-oscuro text-retro-texto overflow-x-hidden relative">
    <Navegacion :cantidad-carrito="cartStore.totalItems" @toggleCarrito="toggleCarrito" />

    <main>
      <RouterView v-slot="{ Component }">
        <component :is="Component" @añadir-al-carrito="añadirAlCarrito" />
      </RouterView>
    </main>

    <PieDePagina />

    <ModalCarrito :abierto="carritoAbierto" :items="cartStore.items" :total="cartStore.totalPrice" @cerrar="toggleCarrito"
      @eliminar="eliminarDelCarrito" @cambiar-cantidad="cambiarCantidad" @vaciar="vaciarCarrito" @checkout="procesarCompra" />
  </div>
</template>
