<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()
const categorias = ref([])

const descripcionesCat = {
  'Plataformas': 'Mario, Sonic, Crash Bandicoot y los clásicos de saltos.',
  'RPG': 'Final Fantasy, Pokémon, Zelda y aventuras épicas inolvidables.',
  'Acción': 'Doom, Contra, Metal Slug y pura adrenalina de 16 bits.',
  'Aventura': 'Metroid, Castlevania y exploración en mundos pixelados.',
  'Deportes': 'Los FIFA y NBA que marcaron época en tu infancia.',
  'Carreras': 'Mario Kart, Gran Turismo y velocidad sin límites.',
  'Lucha': 'Street Fighter, Tekken, Mortal Kombat y los reyes del arcade.',
  'Disparos': 'Doom, Quake, Halo y pura puntería virtual.',
  'Estrategia': 'Age of Empires, StarCraft y batallas de pura mente.',
  'Simulación': 'Los Sims, SimCity y constructores de mundos virtuales.',
  'Puzzle': 'Tetris, Portal y rompecabezas para estrujarte el cerebro.',
  'Terror': 'Resident Evil, Silent Hill y pesadillas en la oscuridad.',
}

onMounted(async () => {
  try {
    const res = await api.get('/categorias')
    categorias.value = res.data.map(cat => ({
      ...cat,
      descripcion: descripcionesCat[cat.nombre] || 'Explora los mejores títulos de esta categoría.',
    }))
  } catch (err) {
    console.error('Error cargando categorías:', err)
  }
})

const irACategoria = (nombre) => {
  router.push({ path: '/productos', query: { categoria: nombre } })
}
</script>

<template>
  <section id="categorias" class="contenedor animate-in slide-in-from-bottom-4 duration-500">
    <div class="mb-10 text-center">
      <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Categorías</h2>
      <p class="text-stone-500 mt-2">Explora nuestras colecciones por género y estilo</p>
      <div class="h-1 w-24 bg-retro-amarillo mx-auto mt-4"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="cat in categorias" :key="cat.id_categoria ?? cat.id"
        @click="irACategoria(cat.nombre)"
        class="group bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 text-center cursor-pointer hover:border-retro-amarillo hover:-translate-y-1 transition-all shadow-sm">
        <div
          class="w-16 h-16 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-6 border border-stone-200 group-hover:border-retro-amarillo transition-colors">
          <!-- Plataformas -->
          <svg v-if="cat.nombre === 'Plataformas'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
          <!-- RPG -->
          <svg v-else-if="cat.nombre === 'RPG'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
          <!-- Acción -->
          <svg v-else-if="cat.nombre === 'Acción'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          <!-- Aventura -->
          <svg v-else-if="cat.nombre === 'Aventura'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          <!-- Deportes -->
          <svg v-else-if="cat.nombre === 'Deportes'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
          <!-- Carreras -->
          <svg v-else-if="cat.nombre === 'Carreras'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
          <!-- Lucha -->
          <svg v-else-if="cat.nombre === 'Lucha'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
          <!-- Disparos -->
          <svg v-else-if="cat.nombre === 'Disparos'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v2m0 12v2m8-8h-2M6 12H4m12 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
          <!-- Estrategia -->
          <svg v-else-if="cat.nombre === 'Estrategia'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
          <!-- Simulación -->
          <svg v-else-if="cat.nombre === 'Simulación'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
          <!-- Puzzle -->
          <svg v-else-if="cat.nombre === 'Puzzle'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
          <!-- Terror -->
          <svg v-else-if="cat.nombre === 'Terror'" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
          <!-- Genérico -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-retro-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
        </div>
        <h3 class="text-xl font-bold text-retro-texto mb-2">{{ cat.nombre }}</h3>
        <p class="text-stone-500 text-sm mb-6 px-4">{{ cat.descripcion }}</p>
        <div class="inline-flex items-center gap-1.5 bg-retro-amarillo text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase shadow-sm">
          Ver juegos
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </div>
      </div>
    </div>
  </section>
</template>
