<script setup>
import { useAuthStore } from '../stores/authStore';

const authStore = useAuthStore();
const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'

defineProps({
  abierto: Boolean,
  items: Array,
  total:  Number
})

const emits = defineEmits(['cerrar', 'eliminar', 'vaciar', 'checkout'])

const esMio = (item) => {
  return authStore.isAuthenticated && authStore.user?.id_usuario === item.id_vendedor
}
</script>

<template>
  <Transition name="fade">
    <div v-if="abierto" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm" @click="emits('cerrar')"></div>
  </Transition>

  <Transition name="slide">
    <aside v-if="abierto"
      class="fixed right-0 top-0 h-full w-full max-w-md bg-retro-gris border-l-4 border-retro-amarillo z-[101] shadow-2xl flex flex-col p-6 overflow-hidden">
      <div class="flex justify-between items-center mb-10 border-b border-stone-200 pb-4">
        <h2 class="text-2xl font-bold text-retro-texto uppercase tracking-tight">Tu Carrito</h2>
        <button @click="emits('cerrar')" class="text-stone-400 hover:text-retro-amarillo transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="flex-1 overflow-y-auto pr-2 space-y-4">
        <div v-if="items.length === 0" class="h-full flex flex-col items-center justify-center opacity-30 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-stone-300 mb-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <p class="font-bold uppercase tracking-widest text-xs text-stone-400">El carrito está vacío</p>
        </div>

        <div v-for="item in items" :key="item.id"
          class="bg-retro-gris border border-stone-200 rounded-xl p-4 flex gap-4 animate-in slide-in-from-right-4 duration-300">
          <div v-if="item.imagen" class="w-20 h-20 rounded-lg overflow-hidden shrink-0 border border-stone-200">
            <img :src="`${apiUrl}${item.imagen}`" :alt="item.titulo" class="w-full h-full object-cover">
          </div>
          <div v-else
            class="w-20 h-20 bg-stone-100 rounded-lg flex items-center justify-center text-[10px] text-stone-400 italic border border-stone-200 shrink-0 uppercase font-mono">
            Foto
          </div>
          <div class="flex-1 flex flex-col">
            <div class="flex justify-between items-start">
              <h3 class="text-retro-texto font-bold text-sm leading-tight truncate w-32">{{ item.titulo }}</h3>
              <button @click="emits('eliminar', item.id)" class="text-gray-600 hover:text-red-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
            <div v-if="esMio(item)" class="mt-1">
              <span class="bg-red-50 text-[#B22222] text-[10px] px-2 py-0.5 rounded border border-red-200 font-bold uppercase">Tu producto</span>
            </div>
            <p class="text-retro-amarillo font-bold text-lg mt-auto">{{ item.precio }}€</p>
          </div>
        </div>
      </div>

      <div class="mt-8 pt-6 border-t border-stone-200 space-y-6">
        <div class="flex justify-between items-end">
          <span class="text-xs font-bold text-stone-400 uppercase tracking-widest">Total a pagar</span>
          <span class="text-3xl font-bold text-retro-amarillo drop-shadow-md">{{ total }}€</span>
        </div>

        <div class="space-y-3">
          <button class="btn-retro-primary w-full py-4 uppercase font-bold text-lg tracking-tight disabled:opacity-50"
            :disabled="items.length === 0" @click="emits('checkout')">
            Finalizar Compra
          </button>
          <button @click="emits('vaciar')"
            class="w-full text-xs text-stone-400 hover:text-retro-amarillo font-bold uppercase tracking-widest transition-colors py-2"
            :disabled="items.length === 0">
            Vaciar Carrito
          </button>
        </div>
      </div>
    </aside>
  </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
}
</style>
