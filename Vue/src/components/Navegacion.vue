<script setup>
import { RouterLink, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const route = useRoute()

defineProps({
  cantidadCarrito: {
    type: Number,
    required: true
  }
})

const emits = defineEmits(['toggleCarrito'])
</script>

<template>
  <nav class="bg-retro-gris border-b-4 border-retro-amarillo sticky top-0 z-50 shadow-md">
    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
      <div class="flex items-center gap-4">
        <h1 class="text-3xl font-bold text-retro-amarillo tracking-wider">RetroColl</h1>
      </div>
      <div class="flex flex-wrap justify-center items-center gap-2 sm:gap-6">
        <router-link to="/"
          class="text-stone-600 hover:text-retro-amarillo transition-colors px-3 py-2 rounded-md hover:bg-red-50">Inicio</router-link>
        <router-link to="/categorias"
          class="text-stone-600 hover:text-retro-amarillo transition-colors px-3 py-2 rounded-md hover:bg-red-50">Categorías</router-link>
        <router-link to="/productos"
          class="text-stone-600 hover:text-retro-amarillo transition-colors px-3 py-2 rounded-md hover:bg-red-50">Productos</router-link>
        <router-link v-if="authStore.isAuthenticated" to="/venta"
          :active-class="route.query.edit ? '' : 'router-link-active'"
          class="text-stone-600 hover:text-retro-amarillo transition-colors px-3 py-2 rounded-md hover:bg-red-50">Venta</router-link>

        <router-link v-if="authStore.isAdmin" to="/dashboard"
          class="text-white bg-retro-amarillo hover:bg-[#8B1111] transition-colors px-3 py-2 rounded-md font-bold text-sm flex items-center gap-1.5 shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd" />
          </svg>
          Admin
        </router-link>

        <!-- Carrito -->
        <button @click="emits('toggleCarrito')"
          class="relative p-2 text-stone-500 hover:text-retro-amarillo transition-colors group">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span v-if="cantidadCarrito > 0"
            class="absolute -top-1 -right-1 bg-retro-amarillo text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full shadow-lg border-2 border-retro-gris animate-in zoom-in duration-300">
            {{ cantidadCarrito }}
          </span>
        </button>

        <router-link v-if="!authStore.isAuthenticated" to="/login"
          class="bg-retro-amarillo text-white font-bold px-4 py-2 rounded shadow-lg hover:bg-[#8B1111] transition-all transform hover:scale-105">Iniciar
          Sesión</router-link>
        <router-link v-else to="/perfil"
          class="bg-retro-amarillo text-white font-bold px-4 py-2 rounded shadow-lg hover:bg-[#8B1111] transition-all transform hover:scale-105">Mi
          Perfil</router-link>
      </div>
    </div>
  </nav>
</template>

<style scoped>
@reference "../assets/tailwind.css";

.router-link-active {
  @apply text-retro-amarillo bg-red-50;
}
</style>
