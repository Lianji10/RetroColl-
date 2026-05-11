<script setup>
import { ref } from 'vue'

const faqs = ref([
  {
    pregunta: '¿Cómo puedo publicar un artículo para vender?',
    respuesta: 'Para publicar un artículo, primero debes registrarte e iniciar sesión. Luego, haz clic en el botón "Vender" en la barra superior. Completa el formulario con el título, descripción, precio, categoría, plataforma y sube una imagen de tu juego. ¡Así de fácil!',
    abierto: false
  },
  {
    pregunta: '¿Cómo funciona el proceso de compra?',
    respuesta: 'Navega por el catálogo y haz clic en "Añadir al Carrito" en los juegos que desees. Puedes revisar tu pedido abriendo el carrito en la parte superior derecha. Cuando estés listo, haz clic en "Checkout" para confirmar la transacción.',
    abierto: false
  },
  {
    pregunta: '¿Puedo comprar mis propios productos?',
    respuesta: 'No, por seguridad y coherencia del ecosistema, RetroColl bloquea automáticamente cualquier intento de un usuario de añadir o comprar los artículos que él mismo ha puesto en venta.',
    abierto: false
  },
  {
    pregunta: '¿Cómo funcionan las valoraciones?',
    respuesta: 'Una vez que hayas comprado un artículo, tendrás la opción de dejar una valoración de 1 a 5 estrellas y un comentario al vendedor. Esto ayuda a construir una comunidad segura y de confianza.',
    abierto: false
  }
])

const toggleFaq = (index) => {
  faqs.value[index].abierto = !faqs.value[index].abierto
}
</script>

<template>
  <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8 min-h-[70vh]">
    <div class="text-center mb-12">
      <h1 class="text-3xl font-extrabold text-stone-800 sm:text-4xl">
        Ayuda y Preguntas Frecuentes
      </h1>
      <p class="mt-4 text-lg text-stone-600">
        Resuelve tus dudas rápidamente para disfrutar al máximo de RetroColl.
      </p>
    </div>

    <div class="space-y-4">
      <div 
        v-for="(faq, index) in faqs" 
        :key="index"
        class="border border-stone-200 rounded-lg bg-white overflow-hidden shadow-sm hover:border-retro-amarillo transition-colors"
      >
        <button 
          @click="toggleFaq(index)"
          class="w-full px-6 py-4 flex justify-between items-center bg-white hover:bg-stone-50 transition-colors focus:outline-none"
        >
          <span class="font-medium text-stone-800 text-left">{{ faq.pregunta }}</span>
          <span class="ml-6 flex-shrink-0">
            <svg 
              class="h-5 w-5 text-retro-amarillo transform transition-transform duration-200" 
              :class="{ 'rotate-180': faq.abierto }"
              xmlns="http://www.w3.org/2000/svg" 
              viewBox="0 0 20 20" 
              fill="currentColor" 
              aria-hidden="true"
            >
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </span>
        </button>
        
        <div 
          v-show="faq.abierto"
          class="px-6 pb-4 pt-2 border-t border-stone-100 bg-stone-50 text-stone-600"
        >
          <p>{{ faq.respuesta }}</p>
        </div>
      </div>
    </div>
    
    <div class="mt-12 text-center p-6 bg-stone-100 border border-stone-200 rounded-lg">
      <h3 class="text-lg font-medium text-stone-800">¿No encuentras lo que buscas?</h3>
      <p class="mt-2 text-stone-600">Ponte en contacto con nosotros escribiendo a <a href="mailto:retrocoll@gmail.com" class="text-retro-amarillo font-bold hover:underline">retrocoll@gmail.com</a></p>
    </div>
  </div>
</template>
