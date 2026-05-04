<template>
    <section id="vender" class="contenedor animate-in fade-in slide-in-from-bottom-6 duration-500">
        <div class="max-w-3xl mx-auto">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Vender Videojuego</h2>
                <p class="text-stone-500 mt-2">Pon tu joya en manos de otro coleccionista apasionado</p>
                <div class="h-1 w-24 bg-retro-amarillo mx-auto mt-4"></div>
            </div>

            <!-- Mensaje de éxito -->
            <div v-if="exito"
                class="mb-6 p-4 bg-green-50 border border-green-300 text-green-700 rounded-xl text-center font-bold animate-in zoom-in-95 duration-300">
                ✅ ¡Juego publicado con éxito! Redirigiendo a productos...
            </div>

            <!-- Mensaje de error -->
            <div v-if="errorMsg"
                class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-xl text-center text-sm">
                ⚠️ {{ errorMsg }}
            </div>

            <form @submit.prevent="publicarJuego"
                class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 md:p-12 shadow-lg space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Título del juego</label>
                        <input type="text" v-model="form.titulo" required placeholder="Ej: Super Mario 64"
                            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
                    </div>

                    <!-- Categoría (desde API) -->
                    <div>
                        <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Categoría</label>
                        <select v-model="form.id_categoria" required
                            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer">
                            <option value="">
                                {{ cargando ? 'Cargando...' : 'Selecciona una categoría' }}
                            </option>
                            <option v-for="cat in categorias" :key="cat.id_categoria" :value="cat.id_categoria">
                                {{ cat.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Plataforma (desde API) -->
                    <div>
                        <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Plataforma</label>
                        <select v-model="form.id_plataforma" required
                            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer">
                            <option value="">
                                {{ cargando ? 'Cargando...' : 'Selecciona la consola' }}
                            </option>
                            <option v-for="plat in plataformas" :key="plat.id_plataforma" :value="plat.id_plataforma">
                                {{ plat.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Precio -->
                    <div>
                        <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Precio (€)</label>
                        <input type="number" v-model="form.precio" required placeholder="45" min="0" step="0.01"
                            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all">
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Estado</label>
                        <select v-model="form.estado" required
                            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto focus:border-retro-amarillo outline-none transition-all cursor-pointer">
                            <option value="">Selecciona el estado</option>
                            <option value="Nuevo">Nuevo - Sellado</option>
                            <option value="Como nuevo">Como nuevo - Con caja</option>
                            <option value="Buen estado">Buen estado - Con caja</option>
                            <option value="Sin caja">Buen estado - Sin caja</option>
                            <option value="Aceptable">Estado aceptable</option>
                        </select>
                    </div>
                    <!-- Imagen -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Imagen del juego</label>
                        <input type="file" @change="handleImage" accept="image/*" required
                            class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-retro-amarillo hover:file:bg-blue-100 transition-all cursor-pointer">
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-xs font-bold text-retro-amarillo uppercase mb-2">Descripción Detallada</label>
                    <textarea v-model="form.descripcion" required
                        placeholder="Describe el estado del juego, manuales, estética de la caja..."
                        class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-3 text-retro-texto placeholder-stone-400 focus:border-retro-amarillo outline-none transition-all min-h-[150px]"></textarea>
                </div>

                <button type="submit" :disabled="enviando"
                    class="btn-retro-primary w-full py-5 text-xl uppercase font-bold disabled:opacity-60 disabled:cursor-not-allowed">
                    {{ enviando ? 'Publicando...' : '📦 Publicar Juego' }}
                </button>
            </form>
        </div>
    </section>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const form = reactive({
    titulo: '',
    id_categoria: '',
    id_plataforma: '',
    precio: '',
    estado: '',
    descripcion: '',
    imagen: null
})

const categorias = ref([])
const plataformas = ref([])
const cargando = ref(true)
const enviando = ref(false)
const exito = ref(false)
const errorMsg = ref('')

const handleImage = (event) => {
    form.imagen = event.target.files[0]
}

// Cargar categorías y plataformas desde la API al montar
onMounted(async () => {
    try {
        const [resCats, resPlats] = await Promise.all([
            api.get('/categorias'),
            api.get('/plataformas')
        ])
        categorias.value = resCats.data
        plataformas.value = resPlats.data
    } catch (err) {
        errorMsg.value = 'No se pudieron cargar las categorías o plataformas. Comprueba que el servidor Laravel está activo.'
    } finally {
        cargando.value = false
    }
})

const publicarJuego = async () => {
    errorMsg.value = ''
    enviando.value = true

    try {
        const formData = new FormData()
        formData.append('titulo', form.titulo)
        formData.append('descripcion', form.descripcion)
        formData.append('precio', parseFloat(form.precio))
        formData.append('estado', form.estado)
        formData.append('id_categoria', form.id_categoria)
        formData.append('id_plataforma', form.id_plataforma)
        if (form.imagen) {
            formData.append('imagen', form.imagen)
        }

        await api.post('/productos', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        exito.value = true

        // Redirigir a productos tras 1.5 segundos
        setTimeout(() => {
            router.push('/productos')
        }, 1500)

    } catch (err) {
        if (err.response?.status === 401) {
            errorMsg.value = 'Debes iniciar sesión para publicar un juego.'
        } else if (err.response?.data?.errors) {
            const errores = Object.values(err.response.data.errors).flat()
            errorMsg.value = errores.join(' | ')
        } else {
            errorMsg.value = err.response?.data?.message || 'Error al publicar el juego. Inténtalo de nuevo.'
        }
    } finally {
        enviando.value = false
    }
}
</script>
