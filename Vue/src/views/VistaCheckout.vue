<script setup>
import { ref, reactive, computed } from 'vue'
import { useCartStore } from '../stores/cartStore'
import { useAuthStore } from '../stores/authStore'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const authStore = useAuthStore()
const router = useRouter()
const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'

const cartItems = computed(() => cartStore.items)
const totalCarrito = computed(() => cartStore.totalPrice)

// Detectar si hay productos propios en el carrito (defensa en profundidad)
const itemsPropios = computed(() => {
  if (!authStore.isAuthenticated || !authStore.user) return []
  return cartStore.items.filter(item => item.id_vendedor === authStore.user.id_usuario)
})
const tieneItemsPropios = computed(() => itemsPropios.value.length > 0)

const procesando = ref(false)
const compraRealizada = ref(false)
const errorGlobal = ref('')

const metodosPago = [
  { valor: 'tarjeta', nombre: 'Tarjeta', icono: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
  { valor: 'transferencia', nombre: 'Transferencia', icono: 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z' },
  { valor: 'paypal', nombre: 'PayPal', icono: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z' },
]

const form = reactive({
  nombre: authStore.user?.nombre || '',
  email: authStore.user?.email || '',
  telefono: '',
  direccion: '',
  ciudad: '',
  cp: '',
  pais: 'España',
  metodoPago: 'tarjeta',
  numeroTarjeta: '',
  caducidad: '',
  cvv: '',
  titular: '',
})

const errors = reactive({})

const formatearTarjeta = () => {
  let val = form.numeroTarjeta.replace(/\D/g, '').substring(0, 16)
  form.numeroTarjeta = val.replace(/(.{4})/g, '$1 ').trim()
}

const validar = () => {
  Object.keys(errors).forEach(k => delete errors[k])
  if (!form.nombre.trim()) errors.nombre = 'El nombre es obligatorio'
  if (!form.email.trim() || !form.email.includes('@')) errors.email = 'Email válido requerido'
  if (!form.direccion.trim()) errors.direccion = 'La dirección es obligatoria'
  if (!form.ciudad.trim()) errors.ciudad = 'La ciudad es obligatoria'
  if (!form.cp.trim() || !/^\d{4,5}$/.test(form.cp)) errors.cp = 'CP inválido'
  if (form.metodoPago === 'tarjeta') {
    const num = form.numeroTarjeta.replace(/\s/g, '')
    if (num.length < 16) errors.numeroTarjeta = 'Número de tarjeta inválido'
    if (!/^\d{2}\/\d{2}$/.test(form.caducidad)) errors.caducidad = 'Formato MM/AA'
    if (form.cvv.length < 3) errors.cvv = 'CVV inválido'
  }
  return Object.keys(errors).length === 0
}

const procesarCompra = async () => {
  if (tieneItemsPropios.value) return // Bloqueo extra por seguridad
  if (!validar()) return
  procesando.value = true
  errorGlobal.value = ''
  try {
    await cartStore.checkout()
    compraRealizada.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (err) {
    errorGlobal.value = err.response?.data?.message || 'Error al procesar la compra. Inténtalo de nuevo.'
  } finally {
    procesando.value = false
  }
}
</script>

<template>
  <section id="checkout" class="contenedor animate-in fade-in slide-in-from-bottom-6 duration-500">

    <!-- Cabecera -->
    <div class="mb-10">
      <button @click="$router.back()"
        class="flex items-center gap-2 text-stone-500 hover:text-retro-amarillo font-bold uppercase text-xs tracking-widest transition-colors group mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-1 transition-transform"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver al carrito
      </button>
      <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Finalizar Compra</h2>
      <div class="h-1 w-24 bg-retro-amarillo mt-2"></div>
    </div>

    <!-- Compra completada -->
    <div v-if="compraRealizada" class="max-w-lg mx-auto text-center py-20 animate-in zoom-in-95 duration-300">
      <div
        class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-green-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" viewBox="0 0 20 20"
          fill="currentColor">
          <path fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l3-3z"
            clip-rule="evenodd" />
        </svg>
      </div>
      <h3 class="text-2xl font-bold text-retro-texto uppercase tracking-tight mb-3">¡Compra realizada!</h3>
      <p class="text-stone-500 mb-8">Tu pedido ha sido procesado correctamente. Recibirás los artículos en la dirección
        indicada.</p>
      <div class="bg-retro-gris border-2 border-stone-200 rounded-xl p-6 mb-8 text-left">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-3">Resumen del pedido</p>
        <p class="text-sm text-stone-600"><span class="font-bold text-retro-texto">Envío a:</span> {{ form.direccion }},
          {{ form.ciudad }}</p>
        <p class="text-sm text-stone-600 mt-1"><span class="font-bold text-retro-texto">Total pagado:</span> <span
            class="text-retro-amarillo font-bold">{{ totalCarrito.toFixed(2) }}€</span></p>
      </div>
      <router-link to="/productos" class="btn-retro-primary py-4 px-8 text-sm uppercase font-bold inline-block">
        Seguir comprando
      </router-link>
    </div>

    <!-- Formulario de compra -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-10">

      <!-- Columna Formulario -->
      <div class="lg:col-span-2 space-y-8">

        <!-- Datos personales -->
        <div class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 shadow-sm">
          <h3 class="text-sm font-bold text-retro-texto uppercase tracking-widest mb-6 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-retro-amarillo" viewBox="0 0 20 20"
              fill="currentColor">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
            Datos personales
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Nombre completo</label>
              <input v-model="form.nombre" type="text" placeholder="Juan García"
                :class="errors.nombre ? 'border-red-300' : 'border-stone-200'"
                class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm" />
              <p v-if="errors.nombre" class="text-[#B22222] text-xs mt-1">{{ errors.nombre }}</p>
            </div>
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Email</label>
              <input v-model="form.email" type="email" placeholder="juan@ejemplo.com"
                :class="errors.email ? 'border-red-300' : 'border-stone-200'"
                class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm" />
              <p v-if="errors.email" class="text-[#B22222] text-xs mt-1">{{ errors.email }}</p>
            </div>
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Teléfono</label>
              <input v-model="form.telefono" type="tel" placeholder="600 123 456"
                class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm" />
            </div>
          </div>
        </div>

        <!-- Dirección de envío -->
        <div class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 shadow-sm">
          <h3 class="text-sm font-bold text-retro-texto uppercase tracking-widest mb-6 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-retro-amarillo" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Dirección de envío
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Dirección</label>
              <input v-model="form.direccion" type="text" placeholder="Calle Mayor 24, 3ºA"
                :class="errors.direccion ? 'border-red-300' : 'border-stone-200'"
                class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm" />
              <p v-if="errors.direccion" class="text-[#B22222] text-xs mt-1">{{ errors.direccion }}</p>
            </div>
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Ciudad</label>
              <input v-model="form.ciudad" type="text" placeholder="Madrid"
                :class="errors.ciudad ? 'border-red-300' : 'border-stone-200'"
                class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm" />
              <p v-if="errors.ciudad" class="text-[#B22222] text-xs mt-1">{{ errors.ciudad }}</p>
            </div>
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Código Postal</label>
              <input v-model="form.cp" type="text" placeholder="28001" maxlength="5"
                :class="errors.cp ? 'border-red-300' : 'border-stone-200'"
                class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm" />
              <p v-if="errors.cp" class="text-[#B22222] text-xs mt-1">{{ errors.cp }}</p>
            </div>
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">País</label>
              <select v-model="form.pais"
                class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all text-sm cursor-pointer">
                <option value="España">España</option>
                <option value="Portugal">Portugal</option>
                <option value="Francia">Francia</option>
                <option value="Alemania">Alemania</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Método de pago -->
        <div class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 shadow-sm">
          <h3 class="text-sm font-bold text-retro-texto uppercase tracking-widest mb-6 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-retro-amarillo" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            Método de pago
          </h3>

          <!-- Selector de método -->
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
            <button v-for="m in metodosPago" :key="m.valor" @click="form.metodoPago = m.valor" :class="form.metodoPago === m.valor
              ? 'border-retro-amarillo bg-retro-amarillo/5 text-retro-amarillo'
              : 'border-stone-200 text-stone-500 hover:border-stone-300'"
              class="border-2 rounded-xl p-4 text-center transition-all">
              <component :is="'svg'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="m.icono" />
              </component>
              <span class="text-xs font-bold uppercase tracking-wider">{{ m.nombre }}</span>
            </button>
          </div>

          <!-- Tarjeta de crédito -->
          <div v-if="form.metodoPago === 'tarjeta'" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Número de tarjeta</label>
              <input v-model="form.numeroTarjeta" type="text" placeholder="1234 5678 9012 3456" maxlength="19"
                @input="formatearTarjeta" :class="errors.numeroTarjeta ? 'border-red-300' : 'border-stone-200'"
                class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm font-mono" />
              <p v-if="errors.numeroTarjeta" class="text-[#B22222] text-xs mt-1">{{ errors.numeroTarjeta }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Caducidad</label>
                <input v-model="form.caducidad" type="text" placeholder="MM/AA" maxlength="5"
                  :class="errors.caducidad ? 'border-red-300' : 'border-stone-200'"
                  class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm font-mono" />
                <p v-if="errors.caducidad" class="text-[#B22222] text-xs mt-1">{{ errors.caducidad }}</p>
              </div>
              <div>
                <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">CVV</label>
                <input v-model="form.cvv" type="password" placeholder="•••" maxlength="4"
                  :class="errors.cvv ? 'border-red-300' : 'border-stone-200'"
                  class="w-full bg-stone-50 border-2 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm font-mono" />
                <p v-if="errors.cvv" class="text-[#B22222] text-xs mt-1">{{ errors.cvv }}</p>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1.5">Titular de la tarjeta</label>
              <input v-model="form.titular" type="text" placeholder="JUAN GARCIA"
                class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-300 focus:border-retro-amarillo outline-none transition-all text-sm uppercase" />
            </div>
          </div>

          <!-- Transferencia -->
          <div v-else-if="form.metodoPago === 'transferencia'"
            class="bg-stone-50 border border-stone-200 rounded-xl p-5 text-sm text-stone-600">
            <p class="font-bold text-retro-texto mb-2">Datos bancarios para la transferencia:</p>
            <p>IBAN: <span class="font-mono font-bold">ES12 3456 7890 1234 5678 9012</span></p>
            <p class="mt-1">Concepto: <span class="font-bold">RetroColl - Pedido</span></p>
            <p class="mt-3 text-xs text-stone-400">El pedido se procesará una vez confirmemos la transferencia (1-2 días
              hábiles).</p>
          </div>

          <!-- PayPal -->
          <div v-else-if="form.metodoPago === 'paypal'"
            class="bg-stone-50 border border-stone-200 rounded-xl p-5 text-sm text-stone-600 text-center">
            <p class="font-bold text-retro-texto mb-2">Serás redirigido a PayPal al confirmar la compra.</p>
            <p class="text-xs text-stone-400">Tu transacción estará protegida por PayPal.</p>
          </div>
        </div>

          <!-- Aviso artículos propios -->
          <div v-if="tieneItemsPropios"
            class="p-4 bg-red-50 border-2 border-[#B22222] text-[#B22222] rounded-xl text-sm font-bold flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="mb-1">No puedes comprar tus propios artículos.</p>
              <p class="text-xs font-normal text-red-500">Elimina del carrito los productos que son tuyos para continuar.</p>
            </div>
          </div>

          <!-- Error global -->
          <div v-if="errorGlobal"
          class="p-4 bg-red-50 border border-red-300 text-[#B22222] rounded-xl text-sm font-bold flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd" />
          </svg>
          {{ errorGlobal }}
        </div>
      </div>

      <!-- Columna Resumen -->
      <div class="lg:col-span-1">
        <div class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-6 shadow-sm sticky top-24">
          <h3 class="text-sm font-bold text-retro-texto uppercase tracking-widest mb-5">Resumen del pedido</h3>

          <div class="space-y-3 mb-5">
            <div v-for="item in cartItems" :key="item.id" class="flex items-center gap-3">
              <div v-if="item.imagen" class="w-12 h-12 rounded-lg overflow-hidden border border-stone-200 shrink-0">
                <img :src="`${apiUrl}${item.imagen}`" :alt="item.titulo"
                  class="w-full h-full object-cover" />
              </div>
              <div v-else
                class="w-12 h-12 bg-stone-100 rounded-lg border border-stone-200 shrink-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-stone-300" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-retro-texto truncate">{{ item.titulo }}</p>
              </div>
              <p class="text-sm font-bold text-retro-amarillo shrink-0">{{ item.precio }}€</p>
            </div>
          </div>

          <div class="border-t border-stone-200 pt-4 space-y-2">
            <div class="flex justify-between text-sm text-stone-500">
              <span>Subtotal</span>
              <span>{{ totalCarrito.toFixed(2) }}€</span>
            </div>
            <div class="flex justify-between text-sm text-stone-500">
              <span>Envío</span>
              <span class="text-green-600 font-bold">Gratis</span>
            </div>
            <div class="flex justify-between text-base font-bold text-retro-texto pt-2 border-t border-stone-200">
              <span>Total</span>
              <span class="text-retro-amarillo text-xl">{{ totalCarrito.toFixed(2) }}€</span>
            </div>
          </div>

          <button @click="procesarCompra" :disabled="procesando || cartItems.length === 0 || tieneItemsPropios"
            class="btn-retro-primary w-full py-5 text-base uppercase font-bold mt-6 flex items-center justify-center gap-3 disabled:opacity-60 disabled:cursor-not-allowed">
            <svg v-if="!procesando" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ procesando ? 'Procesando...' : 'Confirmar compra' }}
          </button>

          <p class="text-center text-xs text-stone-400 mt-4 flex items-center justify-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                clip-rule="evenodd" />
            </svg>
            Pago 100% seguro y protegido
          </p>
        </div>
      </div>

    </div>
  </section>
</template>
