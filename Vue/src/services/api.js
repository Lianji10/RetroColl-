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

// Interceptor de respuesta: redirigir a /login si el token expiró (401)
api.interceptors.response.use(
  (response) => response,
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
