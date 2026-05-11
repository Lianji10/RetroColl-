<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const guardando = ref(false)
const mensajeError = ref('')
const mensajeExito = ref('')

const form = ref({
  nombre: '',
  email: '',
  password_actual: '',
  password: '',
  password_confirmation: ''
})

onMounted(() => {
  if (authStore.user) {
    form.value.nombre = authStore.user.nombre
    form.value.email = authStore.user.email
  }
})

const guardarCambios = async () => {
  mensajeError.value = ''
  mensajeExito.value = ''
  
  if (form.value.password && form.value.password !== form.value.password_confirmation) {
    mensajeError.value = 'Las contraseñas no coinciden.'
    return
  }

  if (form.value.password && !form.value.password_actual) {
    mensajeError.value = 'Debes introducir tu contraseña actual para establecer una nueva.'
    return
  }

  guardando.value = true

  try {
    const payload = {
      nombre: form.value.nombre,
      email: form.value.email
    }

    if (form.value.password) {
      payload.password_actual = form.value.password_actual
      payload.password = form.value.password
    }

    await authStore.actualizarPerfil(payload)
    
    mensajeExito.value = 'Perfil actualizado correctamente.'
    form.value.password_actual = ''
    form.value.password = ''
    form.value.password_confirmation = ''
    
    setTimeout(() => {
      router.push('/perfil')
    }, 1500)
    
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      mensajeError.value = Object.values(errors).flat()[0]
    } else {
      mensajeError.value = error.response?.data?.message || 'Error al actualizar el perfil.'
    }
  } finally {
    guardando.value = false
  }
}
</script>

<template>
  <div class="contenedor py-10 animate-in fade-in duration-500 max-w-2xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Editar Perfil</h2>
        <div class="h-1 w-20 bg-retro-amarillo mt-2"></div>
      </div>
      <router-link to="/perfil" class="text-stone-500 hover:text-retro-amarillo font-bold uppercase tracking-widest text-xs flex items-center gap-2 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver
      </router-link>
    </div>

    <form @submit.prevent="guardarCambios" class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 shadow-lg">
      <div v-if="mensajeError" class="mb-6 p-4 bg-red-50 text-red-600 border border-red-200 rounded-lg text-sm font-medium">
        {{ mensajeError }}
      </div>
      <div v-if="mensajeExito" class="mb-6 p-4 bg-green-50 text-green-600 border border-green-200 rounded-lg text-sm font-medium">
        {{ mensajeExito }}
      </div>

      <div class="space-y-6">
        <div>
          <label class="block text-retro-texto font-bold uppercase tracking-widest text-xs mb-2">Nombre</label>
          <input v-model="form.nombre" type="text" class="w-full bg-white border border-stone-200 rounded-lg px-4 py-3 focus:outline-none focus:border-retro-amarillo transition-colors font-medium text-stone-700" required>
        </div>

        <div>
          <label class="block text-retro-texto font-bold uppercase tracking-widest text-xs mb-2">Correo Electrónico</label>
          <input v-model="form.email" type="email" class="w-full bg-white border border-stone-200 rounded-lg px-4 py-3 focus:outline-none focus:border-retro-amarillo transition-colors font-medium text-stone-700" required>
        </div>

        <div class="pt-6 border-t border-stone-200">
          <h3 class="text-lg font-bold text-retro-texto mb-4 tracking-tight">Cambiar Contraseña (Opcional)</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-retro-texto font-bold uppercase tracking-widest text-xs mb-2">Contraseña Actual</label>
              <input v-model="form.password_actual" type="password" class="w-full bg-white border border-stone-200 rounded-lg px-4 py-3 focus:outline-none focus:border-retro-amarillo transition-colors font-medium text-stone-700" placeholder="Solo si deseas cambiarla">
            </div>

            <div>
              <label class="block text-retro-texto font-bold uppercase tracking-widest text-xs mb-2">Nueva Contraseña</label>
              <input v-model="form.password" type="password" class="w-full bg-white border border-stone-200 rounded-lg px-4 py-3 focus:outline-none focus:border-retro-amarillo transition-colors font-medium text-stone-700" placeholder="Mínimo 8 caracteres">
            </div>
            
            <div>
              <label class="block text-retro-texto font-bold uppercase tracking-widest text-xs mb-2">Confirmar Nueva Contraseña</label>
              <input v-model="form.password_confirmation" type="password" class="w-full bg-white border border-stone-200 rounded-lg px-4 py-3 focus:outline-none focus:border-retro-amarillo transition-colors font-medium text-stone-700" placeholder="Repite la nueva contraseña">
            </div>
          </div>
        </div>

        <div class="pt-6">
          <button type="submit" :disabled="guardando" class="btn-retro-primary w-full py-4 text-sm font-bold tracking-wide uppercase flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
            <svg v-if="guardando" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ guardando ? 'Guardando...' : 'Guardar Cambios' }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>
