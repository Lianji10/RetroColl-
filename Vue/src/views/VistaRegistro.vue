<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const nombre = ref('')
const email = ref('')
const password = ref('')
const passwordConfirm = ref('')
const router = useRouter()
const authStore = useAuthStore()
const errorMsg = ref('')

const registro = async () => {
  errorMsg.value = ''
  if (password.value !== passwordConfirm.value) {
    errorMsg.value = 'Las contraseñas no coinciden'
    return
  }
  try {
    const success = await authStore.register(nombre.value, email.value, password.value)
    if (success) {
      router.push('/perfil')
    }
  } catch (error) {
    errorMsg.value = error.response?.data?.message || 'Error al intentar forjar tu cuenta.'
    console.error('Fallo en registro:', error)
  }
}
</script>

<template>
  <section id="registro"
    class="contenedor flex items-center justify-center min-h-[70vh] animate-in slide-in-from-right-4 duration-500">
    <div
      class="w-full max-w-md bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 md:p-10 shadow-lg relative overflow-hidden">
      <!-- Decoración retro -->
      <div class="absolute top-0 left-0 w-2 h-full bg-retro-amarillo"></div>

      <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Crear Cuenta</h2>
        <p class="text-stone-500 text-sm mt-1">Registrate en la mayor pagina de venta de videojuegos retro</p>
      </div>

      <div v-if="errorMsg" class="mb-4 p-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm text-center">
        {{ errorMsg }}
      </div>

      <form @submit.prevent="registro" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Nombre
            completo</label>
          <input type="text" v-model="nombre" required placeholder="Juan Pérez"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
        </div>

        <div>
          <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Email</label>
          <input type="email" v-model="email" required placeholder="tu@email.com"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Contraseña</label>
            <input type="password" v-model="password" required placeholder="••••••••"
              class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
          </div>
          <div>
            <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Repetir</label>
            <input type="password" v-model="passwordConfirm" required placeholder="••••••••"
              class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
          </div>
        </div>

        <button type="submit" class="btn-retro-primary w-full py-4 text-lg mt-4">Crear Cuenta</button>
      </form>

      <div class="mt-8 pt-6 border-t border-stone-200 text-center">
        <p class="text-stone-500 text-sm">
          ¿Ya eres miembro?
          <router-link to="/login" class="text-retro-amarillo font-bold hover:underline ml-1">Inicia
            sesión</router-link>
        </p>
      </div>
    </div>
  </section>
</template>
