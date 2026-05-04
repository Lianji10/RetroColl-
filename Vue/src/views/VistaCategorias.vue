<template>
  <section id="categorias" class="contenedor animate-in slide-in-from-bottom-4 duration-500">
    <div class="mb-10 text-center">
      <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Categorías</h2>
      <p class="text-stone-500 mt-2">Explora nuestras colecciones por género y estilo</p>
      <div class="h-1 w-24 bg-retro-amarillo mx-auto mt-4"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="cat in categorias" :key="cat.id"
        class="group bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 text-center cursor-pointer hover:border-retro-amarillo hover:-translate-y-1 transition-all shadow-sm">
        <div
          class="w-16 h-16 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-6 border border-stone-200 group-hover:border-retro-amarillo transition-colors">
          <span class="text-2xl" v-if="cat.nombre === 'Plataformas'">🍄</span>
          <span class="text-2xl" v-else-if="cat.nombre === 'RPG'">⚔️</span>
          <span class="text-2xl" v-else-if="cat.nombre === 'Acción'">🔫</span>
          <span class="text-2xl" v-else-if="cat.nombre === 'Aventura'">🗺️</span>
          <span class="text-2xl" v-else-if="cat.nombre === 'Deportes'">⚽</span>
          <span class="text-2xl" v-else-if="cat.nombre === 'Carreras'">🏎️</span>
        </div>
        <h3 class="text-xl font-bold text-retro-texto mb-2">{{ cat.nombre }}</h3>
        <p class="text-stone-500 text-sm mb-6 px-4">{{ cat.descripcion }}</p>
        <div
          class="inline-block bg-retro-amarillo text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase shadow-sm">
          {{ cat.cantidad }} juegos
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const categorias = [
  { id: 1, nombre: 'Plataformas', descripcion: 'Mario, Sonic, Crash Bandicoot y los clásicos de saltos.', cantidad: 45 },
  { id: 2, nombre: 'RPG', descripcion: 'Final Fantasy, Pokémon, Zelda y aventuras épicas inolvidables.', cantidad: 38 },
  { id: 3, nombre: 'Acción', descripcion: 'Doom, Contra, Metal Slug y pura adrenalina de 16 bits.', cantidad: 52 },
  { id: 4, nombre: 'Aventura', descripcion: 'Metroid, Castlevania y exploración en mundos pixelados.', cantidad: 34 },
  { id: 5, nombre: 'Deportes', descripcion: 'Los FIFA y NBA que marcaron época en tu infancia.', cantidad: 28 },
  { id: 6, nombre: 'Carreras', descripcion: 'Mario Kart, Gran Turismo y velocidad sin límites.', cantidad: 26 }
]
</script>
