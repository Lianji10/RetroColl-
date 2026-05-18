import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE || 'http://localhost:8000/api',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }
});

// Inyectar token en cada petición
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Interceptor de respuesta: limpiar basura antes del JSON y gestionar errores ya que al recibir la respuesta de la API muestra unos asteriscos delante del JSON
api.interceptors.response.use(
  (response) => {
    // Si Axios no pudo parsear el JSON porque tiene caracteres invisibles (BOM) delante
    if (typeof response.data === 'string') {
      const firstBrace = response.data.indexOf('{');
      const firstBracket = response.data.indexOf('[');
      let startIndex = -1;
      
      if (firstBrace !== -1 && firstBracket !== -1) {
        startIndex = Math.min(firstBrace, firstBracket);
      } else if (firstBrace !== -1) {
        startIndex = firstBrace;
      } else if (firstBracket !== -1) {
        startIndex = firstBracket;
      }

      if (startIndex > 0) { 
        try {
          const jsonStr = response.data.substring(startIndex);
          response.data = JSON.parse(jsonStr);
        } catch (e) {
          console.warn("No se pudo limpiar el JSON:", e);
        }
      }
    }
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      localStorage.removeItem('retrocoll_carrito');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export const createValoracion = (data) => api.post('/valoraciones', data);
export const getValoracionesUsuario = (id) => api.get(`/usuarios/${id}/valoraciones`);

export default api;
