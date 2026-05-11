<script setup>
import { ref } from 'vue'
import { createValoracion } from '../services/api'

const props = defineProps({
  receptorId: {
    type: Number,
    required: true
  },
  receptorNombre: {
    type: String,
    default: 'Vendedor'
  }
})

const emit = defineEmits(['cerrar', 'completado'])

const puntuacion = ref(5)
const comentario = ref('')
const enviando = ref(false)
const error = ref('')

const enviarValoracion = async () => {
  if (puntuacion.value < 1 || puntuacion.value > 5) {
    error.value = 'Por favor, selecciona una puntuación entre 1 y 5'
    return
  }

  enviando.value = true
  error.value = ''

  try {
    await createValoracion({
      puntuacion: puntuacion.value,
      comentario: comentario.value,
      id_receptor: props.receptorId
    })
    emit('completado')
    emit('cerrar')
  } catch (e) {
    console.error('Error al enviar valoración', e)
    error.value = e.response?.data?.message || 'Error al enviar la valoración. Inténtalo de nuevo.'
  } finally {
    enviando.value = false
  }
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white border border-stone-200 rounded-xl w-full max-w-md overflow-hidden shadow-xl">
      <!-- Header -->
      <div class="bg-retro-amarillo p-5 text-white relative">
        <button @click="emit('cerrar')" class="absolute top-4 right-4 text-white/80 hover:text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <h3 class="text-lg font-bold uppercase tracking-tight">Valorar a {{ receptorNombre }}</h3>
      </div>

      <!-- Body -->
      <div class="p-6">
        <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg">
          {{ error }}
        </div>

        <!-- Stars Selector -->
        <div class="mb-6">
          <label class="block text-stone-500 text-xs font-bold uppercase tracking-widest mb-3">Puntuación</label>
          <div class="flex gap-1">
            <button v-for="i in 5" :key="i" @click="puntuacion = i" class="focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" 
                :class="i <= puntuacion ? 'text-retro-amarillo fill-current' : 'text-stone-200 fill-none'"
                class="h-8 w-8" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Comment -->
        <div class="mb-6">
          <label class="block text-stone-500 text-xs font-bold uppercase tracking-widest mb-2">Comentario (opcional)</label>
          <textarea v-model="comentario" rows="3" 
            class="w-full bg-stone-50 border border-stone-200 rounded-lg p-3 text-retro-texto focus:border-retro-amarillo focus:ring-0 resize-none"
            placeholder="¿Cómo fue la transacción?"></textarea>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button @click="emit('cerrar')" class="flex-1 border border-stone-200 py-2.5 text-sm font-bold uppercase text-stone-500 rounded-lg hover:bg-stone-50">
            Cancelar
          </button>
          <button @click="enviarValoracion" :disabled="enviando"
            class="flex-1 bg-retro-amarillo py-2.5 text-sm font-bold uppercase text-white rounded-lg disabled:opacity-50">
            <span v-if="enviando">Enviando...</span>
            <span v-else>Enviar</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
