<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useAuthStore } from '../stores/authStore'
import api from '../services/api'

const authStore = useAuthStore()

// ─── Estado ───────────────────────────────────────────────────
const tabActivo = ref('productos')
const stats = ref({})
const productos = ref([])
const usuarios = ref([])
const cargandoProductos = ref(true)
const cargandoUsuarios = ref(true)
const errorMsg = ref('')
const okMsg = ref('')
const buscarProducto = ref('')
const buscarUsuario = ref('')

const modalConfirm = reactive({
  visible: false,
  mensaje: '',
  accion: null,
  ejecutando: false,
})

const modalEdicion = reactive({
  visible: false,
  productoId: null,
  guardando: false,
  error: '',
  form: { titulo: '', precio: '', estado: '', descripcion: '' },
})

// ─── Filtros ──────────────────────────────────────────────────
const productosFiltrados = computed(() => {
  const q = buscarProducto.value.toLowerCase()
  if (!q) return productos.value
  return productos.value.filter(p =>
    p.titulo?.toLowerCase().includes(q) ||
    p.vendedor?.nombre?.toLowerCase().includes(q)
  )
})

const usuariosFiltrados = computed(() => {
  const q = buscarUsuario.value.toLowerCase()
  if (!q) return usuarios.value
  return usuarios.value.filter(u =>
    u.nombre?.toLowerCase().includes(q) ||
    u.email?.toLowerCase().includes(q)
  )
})

// ─── Carga de datos ───────────────────────────────────────────
const cargarTodo = async () => {
  errorMsg.value = ''
  try {
    const [resStats, resProd, resUsers] = await Promise.all([
      api.get('/admin/estadisticas'),
      api.get('/admin/productos'),
      api.get('/admin/usuarios'),
    ])
    stats.value = resStats.data
    productos.value = resProd.data
    usuarios.value = resUsers.data
  } catch (err) {
    errorMsg.value = 'Error al cargar los datos del panel. Comprueba que tienes permisos de administrador.'
  } finally {
    cargandoProductos.value = false
    cargandoUsuarios.value = false
  }
}

onMounted(cargarTodo)

// ─── Helpers ──────────────────────────────────────────────────
const formatFecha = (fecha) => {
  if (!fecha) return '—'
  return new Date(fecha).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' })
}

const mostrarOk = (msg) => {
  okMsg.value = msg
  setTimeout(() => { okMsg.value = '' }, 3000)
}

// ─── Productos ────────────────────────────────────────────────
const abrirEdicion = (p) => {
  modalEdicion.productoId = p.id_producto
  modalEdicion.form.titulo = p.titulo
  modalEdicion.form.precio = p.precio
  modalEdicion.form.estado = p.estado
  modalEdicion.form.descripcion = p.descripcion || ''
  modalEdicion.error = ''
  modalEdicion.visible = true
}

const guardarEdicion = async () => {
  modalEdicion.guardando = true
  modalEdicion.error = ''
  try {
    const res = await api.put(`/productos/${modalEdicion.productoId}`, {
      titulo: modalEdicion.form.titulo,
      precio: parseFloat(modalEdicion.form.precio),
      estado: modalEdicion.form.estado,
      descripcion: modalEdicion.form.descripcion,
    })
    // Actualizar en la lista local
    const idx = productos.value.findIndex(p => p.id_producto === modalEdicion.productoId)
    if (idx !== -1) {
      productos.value[idx] = { ...productos.value[idx], ...res.data }
    }
    modalEdicion.visible = false
    mostrarOk('Producto actualizado correctamente.')
  } catch (err) {
    modalEdicion.error = err.response?.data?.message || 'Error al guardar los cambios.'
  } finally {
    modalEdicion.guardando = false
  }
}

const confirmarEliminarProducto = (p) => {
  modalConfirm.mensaje = `¿Eliminar el producto "${p.titulo}" de forma permanente?`
  modalConfirm.accion = () => eliminarProducto(p.id_producto)
  modalConfirm.visible = true
}

const eliminarProducto = async (id) => {
  await api.delete(`/admin/productos/${id}`)
  productos.value = productos.value.filter(p => p.id_producto !== id)
  stats.value.total_productos = Math.max(0, (stats.value.total_productos || 1) - 1)
  mostrarOk('Producto eliminado correctamente.')
}

// ─── Usuarios ─────────────────────────────────────────────────
const cambiarRol = async (u, nuevoRol) => {
  try {
    errorMsg.value = ''
    await api.put(`/admin/usuarios/${u.id_usuario}/rol`, { rol: nuevoRol })
    u.rol = nuevoRol
    if (nuevoRol === 'admin') stats.value.total_admins = (stats.value.total_admins || 0) + 1
    else stats.value.total_admins = Math.max(0, (stats.value.total_admins || 1) - 1)
    mostrarOk(`Rol de ${u.nombre} cambiado a "${nuevoRol}".`)
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Error al cambiar el rol.'
  }
}

const confirmarEliminarUsuario = (u) => {
  modalConfirm.mensaje = `¿Eliminar al usuario "${u.nombre}" y todos sus productos? Esta acción es irreversible.`
  modalConfirm.accion = () => eliminarUsuario(u.id_usuario)
  modalConfirm.visible = true
}

const eliminarUsuario = async (id) => {
  await api.delete(`/admin/usuarios/${id}`)
  const u = usuarios.value.find(u => u.id_usuario === id)
  if (u?.rol === 'admin') stats.value.total_admins = Math.max(0, (stats.value.total_admins || 1) - 1)
  usuarios.value = usuarios.value.filter(u => u.id_usuario !== id)
  stats.value.total_usuarios = Math.max(0, (stats.value.total_usuarios || 1) - 1)
  mostrarOk('Usuario eliminado correctamente.')
}

// ─── Modal ────────────────────────────────────────────────────
const ejecutarAccion = async () => {
  if (!modalConfirm.accion) return
  modalConfirm.ejecutando = true
  errorMsg.value = ''
  try {
    await modalConfirm.accion()
    modalConfirm.visible = false
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Error al ejecutar la acción.'
    modalConfirm.visible = false
  } finally {
    modalConfirm.ejecutando = false
    modalConfirm.accion = null
  }
}
</script>

<template>
  <section id="dashboard" class="contenedor animate-in fade-in slide-in-from-bottom-6 duration-500">

    <!-- Header -->
    <div class="mb-10">
      <div class="flex items-center gap-3 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-retro-amarillo" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-3xl font-bold text-retro-texto uppercase tracking-tight">Panel de Administración</h2>
      </div>
      <div class="h-1 w-24 bg-retro-amarillo mt-1"></div>
      <p class="text-stone-500 mt-3 text-sm">Gestiona usuarios y productos de RetroColl</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
      <div class="bg-retro-gris border-2 border-stone-200 rounded-xl p-6 text-center shadow-md hover:border-retro-amarillo transition-all">
        <p class="text-4xl font-bold text-retro-amarillo">{{ stats.total_usuarios ?? '—' }}</p>
        <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mt-2">Usuarios Totales</p>
      </div>
      <div class="bg-retro-gris border-2 border-stone-200 rounded-xl p-6 text-center shadow-md hover:border-retro-amarillo transition-all">
        <p class="text-4xl font-bold text-retro-amarillo">{{ stats.total_productos ?? '—' }}</p>
        <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mt-2">Productos Publicados</p>
      </div>
      <div class="bg-retro-gris border-2 border-stone-200 rounded-xl p-6 text-center shadow-md hover:border-retro-amarillo transition-all">
        <p class="text-4xl font-bold text-retro-amarillo">{{ stats.total_admins ?? '—' }}</p>
        <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mt-2">Administradores</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-6 border-b-2 border-stone-200">
      <button @click="tabActivo = 'productos'"
        :class="tabActivo === 'productos'
          ? 'border-b-2 border-retro-amarillo text-retro-amarillo -mb-[2px]'
          : 'text-stone-500 hover:text-retro-amarillo'"
        class="font-bold uppercase text-xs tracking-widest px-5 py-3 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" /></svg>
        Productos ({{ productos.length }})
      </button>
      <button @click="tabActivo = 'usuarios'"
        :class="tabActivo === 'usuarios'
          ? 'border-b-2 border-retro-amarillo text-retro-amarillo -mb-[2px]'
          : 'text-stone-500 hover:text-retro-amarillo'"
        class="font-bold uppercase text-xs tracking-widest px-5 py-3 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        Usuarios ({{ usuarios.length }})
      </button>
    </div>

    <!-- Mensaje de error global -->
    <div v-if="errorMsg" class="mb-6 p-4 bg-red-50 border border-red-300 text-[#B22222] rounded-xl text-sm font-bold">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
      {{ errorMsg }}
    </div>
    <!-- Mensaje de éxito -->
    <div v-if="okMsg" class="mb-6 p-4 bg-green-50 border border-green-300 text-green-700 rounded-xl text-sm font-bold animate-in zoom-in-95 duration-300">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l3-3z" clip-rule="evenodd" /></svg>
      {{ okMsg }}
    </div>

    <!-- ── TAB PRODUCTOS ────────────────────────────────────── -->
    <div v-if="tabActivo === 'productos'">
      <div v-if="cargandoProductos" class="flex justify-center py-16">
        <div class="w-10 h-10 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin"></div>
      </div>

      <div v-else class="bg-retro-gris border-2 border-stone-200 rounded-xl shadow-md overflow-hidden">
        <!-- Buscador -->
        <div class="p-4 border-b border-stone-200">
          <input v-model="buscarProducto" type="text" placeholder="Buscar por título o vendedor..."
            class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2 text-sm text-retro-texto focus:border-retro-amarillo outline-none transition-all" />
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-stone-100 text-left">
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">ID</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Título</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Vendedor</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Precio</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Categoría</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Plataforma</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Estado</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="productosFiltrados.length === 0">
                <td colspan="8" class="px-5 py-10 text-center text-stone-400 italic">No hay productos.</td>
              </tr>
              <tr v-for="p in productosFiltrados" :key="p.id_producto"
                class="border-t border-stone-100 hover:bg-stone-50 transition-colors">
                <td class="px-5 py-4 font-mono text-stone-400 text-xs">#{{ p.id_producto }}</td>
                <td class="px-5 py-4 font-bold text-retro-texto max-w-[180px] truncate">{{ p.titulo }}</td>
                <td class="px-5 py-4 text-stone-600">{{ p.vendedor?.nombre || '—' }}</td>
                <td class="px-5 py-4 font-bold text-retro-amarillo">{{ p.precio }}€</td>
                <td class="px-5 py-4 text-stone-500">{{ p.categoria?.nombre || '—' }}</td>
                <td class="px-5 py-4 text-stone-500">{{ p.plataforma?.nombre || '—' }}</td>
                <td class="px-5 py-4">
                  <span :class="p.estado === 'Vendido' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-green-100 text-green-700 border-green-200'" class="px-2 py-1 text-[10px] font-bold rounded-full border uppercase tracking-widest">
                    {{ p.estado || 'Disponible' }}
                  </span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex gap-2">
                    <button @click="abrirEdicion(p)"
                      class="text-xs font-bold text-stone-600 border border-stone-300 hover:bg-stone-100 px-3 py-1.5 rounded-lg transition-colors">
                      Editar
                    </button>
                    <button @click="confirmarEliminarProducto(p)"
                      class="text-xs font-bold text-white bg-[#B22222] hover:bg-[#8B1111] px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                      Eliminar
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── TAB USUARIOS ────────────────────────────────────── -->
    <div v-if="tabActivo === 'usuarios'">
      <div v-if="cargandoUsuarios" class="flex justify-center py-16">
        <div class="w-10 h-10 border-4 border-stone-200 border-t-retro-amarillo rounded-full animate-spin"></div>
      </div>

      <div v-else class="bg-retro-gris border-2 border-stone-200 rounded-xl shadow-md overflow-hidden">
        <!-- Buscador -->
        <div class="p-4 border-b border-stone-200">
          <input v-model="buscarUsuario" type="text" placeholder="Buscar por nombre o email..."
            class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2 text-sm text-retro-texto focus:border-retro-amarillo outline-none transition-all" />
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-stone-100 text-left">
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">ID</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Nombre</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Email</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Rol</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Registro</th>
                <th class="px-5 py-3 text-xs font-bold text-stone-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="usuariosFiltrados.length === 0">
                <td colspan="6" class="px-5 py-10 text-center text-stone-400 italic">No hay usuarios.</td>
              </tr>
              <tr v-for="u in usuariosFiltrados" :key="u.id_usuario"
                class="border-t border-stone-100 hover:bg-stone-50 transition-colors"
                :class="u.id_usuario === authStore.user?.id_usuario ? 'bg-red-50/50' : ''">
                <td class="px-5 py-4 font-mono text-stone-400 text-xs">#{{ u.id_usuario }}</td>
                <td class="px-5 py-4 font-bold text-retro-texto flex items-center gap-2">
                  {{ u.nombre }}
                  <span v-if="u.id_usuario === authStore.user?.id_usuario"
                    class="text-[9px] bg-retro-amarillo text-white px-1.5 py-0.5 rounded font-bold uppercase">Tú</span>
                </td>
                <td class="px-5 py-4 text-stone-500">{{ u.email }}</td>
                <td class="px-5 py-4">
                  <span :class="u.rol === 'admin' ? 'bg-red-100 text-[#B22222] border-red-200' : 'bg-stone-100 text-stone-600 border-stone-200'"
                    class="text-[10px] font-bold px-2 py-1 rounded-md border uppercase tracking-wider">
                    {{ u.rol }}
                  </span>
                </td>
                <td class="px-5 py-4 text-stone-400 text-xs">{{ formatFecha(u.created_at) }}</td>
                <td class="px-5 py-4">
                  <div v-if="u.id_usuario !== authStore.user?.id_usuario" class="flex gap-2">
                    <!-- Cambiar rol -->
                    <button v-if="u.rol === 'usuario'" @click="cambiarRol(u, 'admin')"
                      class="text-xs font-bold text-[#B22222] border border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                      → Admin
                    </button>
                    <button v-else @click="cambiarRol(u, 'usuario')"
                      class="text-xs font-bold text-stone-600 border border-stone-200 hover:bg-stone-100 px-3 py-1.5 rounded-lg transition-colors">
                      → Usuario
                    </button>
                    <!-- Eliminar -->
                    <button @click="confirmarEliminarUsuario(u)"
                      class="text-xs font-bold text-white bg-[#B22222] hover:bg-[#8B1111] px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                      Eliminar
                    </button>
                  </div>
                  <span v-else class="text-stone-300 text-xs italic">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Confirmación -->
    <Teleport to="body">
      <div v-if="modalConfirm.visible"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-in fade-in duration-200">
        <div class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 max-w-md w-full shadow-2xl animate-in zoom-in-95 duration-200">
          <div class="text-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-retro-amarillo mx-auto" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <h3 class="text-xl font-bold text-retro-texto mt-4 uppercase tracking-tight">Confirmar acción</h3>
            <p class="text-stone-500 mt-2 text-sm">{{ modalConfirm.mensaje }}</p>
          </div>
          <div class="flex gap-3">
            <button @click="modalConfirm.visible = false"
              class="flex-1 border-2 border-stone-200 text-stone-600 font-bold py-3 rounded-xl hover:bg-stone-100 transition-all uppercase text-xs tracking-widest">
              Cancelar
            </button>
            <button @click="ejecutarAccion" :disabled="modalConfirm.ejecutando"
              class="flex-1 bg-[#B22222] text-white font-bold py-3 rounded-xl hover:bg-[#8B1111] transition-all uppercase text-xs tracking-widest disabled:opacity-60 shadow-lg">
              {{ modalConfirm.ejecutando ? 'Procesando...' : 'Confirmar' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal Edición Producto -->
    <Teleport to="body">
      <div v-if="modalEdicion.visible"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-in fade-in duration-200">
        <div class="bg-retro-gris border-2 border-stone-200 rounded-2xl p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-200">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-retro-texto uppercase tracking-tight">Editar Producto</h3>
            <button @click="modalEdicion.visible = false" class="text-stone-400 hover:text-stone-600 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1">Título</label>
              <input v-model="modalEdicion.form.titulo" type="text"
                class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-2.5 text-retro-texto focus:border-retro-amarillo outline-none transition-all text-sm" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1">Precio (€)</label>
                <input v-model="modalEdicion.form.precio" type="number" step="0.01" min="0"
                  class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-2.5 text-retro-texto focus:border-retro-amarillo outline-none transition-all text-sm" />
              </div>
              <div>
                <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1">Estado</label>
                <select v-model="modalEdicion.form.estado"
                  class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-2.5 text-retro-texto focus:border-retro-amarillo outline-none transition-all text-sm cursor-pointer">
                  <option value="Nuevo">Nuevo - Sellado</option>
                  <option value="Como nuevo">Como nuevo</option>
                  <option value="Buen estado">Buen estado</option>
                  <option value="Sin caja">Sin caja</option>
                  <option value="Aceptable">Aceptable</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-retro-amarillo uppercase mb-1">Descripción</label>
              <textarea v-model="modalEdicion.form.descripcion" rows="3"
                class="w-full bg-stone-50 border-2 border-stone-200 rounded-lg px-4 py-2.5 text-retro-texto focus:border-retro-amarillo outline-none transition-all text-sm resize-none"></textarea>
            </div>
          </div>
          <div v-if="modalEdicion.error" class="mt-4 p-3 bg-red-50 border border-red-200 text-[#B22222] text-xs rounded-lg font-bold">
            {{ modalEdicion.error }}
          </div>
          <div class="flex gap-3 mt-6">
            <button @click="modalEdicion.visible = false"
              class="flex-1 border-2 border-stone-200 text-stone-600 font-bold py-3 rounded-xl hover:bg-stone-100 transition-all uppercase text-xs tracking-widest">
              Cancelar
            </button>
            <button @click="guardarEdicion" :disabled="modalEdicion.guardando"
              class="flex-1 bg-retro-amarillo text-white font-bold py-3 rounded-xl hover:bg-[#8B1111] transition-all uppercase text-xs tracking-widest disabled:opacity-60 shadow-lg">
              {{ modalEdicion.guardando ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </section>
</template>
