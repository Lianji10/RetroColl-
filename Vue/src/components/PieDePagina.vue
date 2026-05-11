<script setup>
import { RouterLink } from 'vue-router'
</script>


<template>
  <footer class="bg-stone-100 border-t-4 border-retro-amarillo text-stone-500 mt-12 py-10 px-4">
    <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-10">
      <div class="space-y-3">
        <h4 class="text-retro-amarillo font-bold text-xl">RetroColl</h4>
        <p class="text-sm">Tu tienda de videojuegos retro favorita</p>
      </div>
      <div class="space-y-3">
        <h4 class="text-retro-amarillo font-bold text-lg">Enlaces Rápidos</h4>
        <div class="flex flex-col gap-2">
          <router-link to="/" class="hover:text-retro-amarillo transition-colors w-fit">Inicio</router-link>
          <router-link to="/categorias"
            class="hover:text-retro-amarillo transition-colors w-fit">Categorías</router-link>
          <router-link to="/productos" class="hover:text-retro-amarillo transition-colors w-fit">Productos</router-link>
          <router-link to="/ayuda" class="hover:text-retro-amarillo transition-colors w-fit font-bold">Ayuda / FAQ</router-link>
        </div>
      </div>
      <div class="space-y-3">
        <h4 class="text-retro-amarillo font-bold text-lg">Contacto</h4>
        <p class="text-sm">Email: retrocoll@gmail.com</p>
        <p class="text-sm">Tel: 679 867 451</p>
      </div>
    </div>
    <div class="max-w-6xl mx-auto border-t border-stone-200 mt-8 pt-6 text-center">
      <p class="text-xs uppercase">RetroColl - Juan Fernández Puchol :)</p>
    </div>
  </footer>
</template>

