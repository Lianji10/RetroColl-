<template>
  <section id="login"
    class="contenedor flex items-center justify-center min-h-[60vh] animate-in zoom-in-95 duration-500">
    <div
      class="w-full max-w-md bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 md:p-10 shadow-lg relative overflow-hidden">
      <!-- Decoración retro -->
      <div class="absolute top-0 left-0 w-2 h-full bg-retro-amarillo"></div>

      <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Iniciar Sesión</h2>
        <p class="text-stone-500 text-sm mt-1">¡Qué bueno verte de nuevo, coleccionista!</p>
      </div>

      <div v-if="errorMsg" class="mb-4 p-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm text-center">
        {{ errorMsg }}
      </div>

      <form @submit.prevent="login" class="space-y-6">
        <div>
          <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Email</label>
          <input type="email" v-model="email" required placeholder="tu@email.com"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
        </div>

        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="block text-xs font-bold text-retro-amarillo uppercase">Contraseña</label>
            <a href="#" class="text-[10px] text-stone-400 hover:text-retro-amarillo transition-colors">¿Olvidaste tu
              contraseña?</a>
          </div>
          <input type="password" v-model="password" required placeholder="••••••••"
            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
        </div>

        <button type="submit" class="btn-retro-primary w-full py-4 text-lg">Iniciar Sesion</button>
      </form>

      <div class="mt-8 pt-6 border-t border-stone-200 text-center">
        <p class="text-stone-500 text-sm">
          ¿No tienes cuenta?
          <router-link to="/registro" class="text-retro-amarillo font-bold hover:underline ml-1">Regístrate </router-link>
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const email = ref('')
const password = ref('')
const router = useRouter()
const authStore = useAuthStore()
const errorMsg = ref('')

const login = async () => {
  errorMsg.value = ''
  try {
    const success = await authStore.login(email.value, password.value)
    if (success) {
      router.push('/perfil')
    }
  } catch (error) {
    errorMsg.value = 'Credenciales incorrectas o error en el servidor.'
    console.error('Login fallido:', error)
  }
}
</script>
