<script setup>
// Vista 404 - página no encontrada
</script>

<template>
  <section class="contenedor flex flex-col items-center justify-center min-h-[60vh] text-center animate-in fade-in duration-500">
    <div class="mb-8">
      <p class="text-9xl font-bold text-stone-100 select-none leading-none">404</p>
      <div class="relative -mt-12">
        <h1 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Página no encontrada</h1>
        <div class="h-1 w-20 bg-retro-amarillo mx-auto mt-3"></div>
      </div>
    </div>
    <p class="text-stone-500 max-w-sm mb-10">
      Este artículo ha sido eliminado, vendido o nunca existió en nuestro catálogo retro.
    </p>
    <div class="flex flex-col sm:flex-row gap-4">
      <router-link to="/productos" class="btn-retro-primary py-4 px-8 text-sm uppercase font-bold">
        Ver el catálogo
      </router-link>
      <button @click="$router.back()" class="border-2 border-stone-200 text-stone-600 font-bold py-4 px-8 rounded-xl hover:bg-stone-100 transition-all uppercase text-sm tracking-wider">
        Volver atrás
      </button>
    </div>
  </section>
</template>
