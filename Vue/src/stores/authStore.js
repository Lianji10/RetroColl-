import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => {
    let userParsed = null;
    try {
      const storedUser = localStorage.getItem('user');
      if (storedUser && storedUser !== 'undefined') {
        userParsed = JSON.parse(storedUser);
      }
    } catch (e) {
      localStorage.removeItem('user');
    }
    
    return {
      user: userParsed,
      token: localStorage.getItem('token') || null,
    };
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.rol === 'admin',
  },
  actions: {
    async login(email, password) {
      try {
        const response = await api.post('/login', { email, password });
        // Laravel responde con access_token
        this.token = response.data.access_token;
        this.user = response.data.usuario;
        
        // Guardar token en LocalStorage
        localStorage.setItem('token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));
        return true;
      } catch (error) {
        console.error('Error al iniciar sesión:', error.response?.data || error.message);
        throw error;
      }
    },
    
    async register(nombre, email, password) {
      try {
        const response = await api.post('/registrar', { nombre, email, password });
        this.token = response.data.access_token;
        this.user = response.data.usuario;
        
        localStorage.setItem('token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));
        return true;
      } catch (error) {
        console.error('Error durante el registro:', error.response?.data || error.message);
        throw error;
      }
    },
    async fetchUser() {
      if (this.token && !this.user) {
        try {
          const response = await api.get('/user');
          this.user = response.data;
          localStorage.setItem('user', JSON.stringify(this.user));
        } catch (error) {
          console.error('Error al obtener el usuario:', error);
          this.token = null;
          this.user = null;
          localStorage.removeItem('token');
          localStorage.removeItem('user');
        }
      }
    },

    async actualizarPerfil(datos) {
      try {
        const response = await api.put('/perfil', datos);
        this.user = response.data.usuario;
        localStorage.setItem('user', JSON.stringify(this.user));
        return response.data;
      } catch (error) {
        console.error('Error al actualizar perfil:', error.response?.data || error.message);
        throw error;
      }
    },

    async logout() {
      try {
        if(this.token) {
          await api.post('/logout');
        }
      } catch (error) {
        console.error('Error al cerrar sesión:', error);
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    }
  }
});
