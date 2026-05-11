import { defineStore } from 'pinia';
import api from '../services/api';
import { useAuthStore } from './authStore';

const CART_KEY = 'retrocoll_carrito';

export const useCartStore = defineStore('cart', {
  state: () => {
    let savedItems = [];
    try {
      savedItems = JSON.parse(localStorage.getItem(CART_KEY)) || [];
      // Normalizar IDs por si había guardados sin la prop 'id'
      savedItems = savedItems.map(item => ({ ...item, id: item.id || item.id_producto }));
    } catch (e) {
      savedItems = [];
    }
    return { items: savedItems };
  },
  getters: {
    totalItems: (state) => state.items.length,
    totalPrice: (state) => state.items.reduce((total, item) => total + Number(item.precio), 0),
  },
  actions: {
    addToCart(producto) {
      // Normalizar el ID ya que puede venir como id o id_producto dependiendo de quién llame
      const pId = producto.id || producto.id_producto;

      // Verificar que el usuario no esté intentando comprar su propio producto
      const authStore = useAuthStore();
      if (authStore.isAuthenticated && authStore.user?.id_usuario === producto.id_vendedor) {
        return; // Silencioso: la UI ya muestra el aviso "Este artículo es tuyo"
      }

      // Cada producto es una publicación única, no se permite duplicar
      const yaEnCarrito = this.items.some(item => item.id === pId);
      if (yaEnCarrito) return;

      this.items.push({ ...producto, id: pId });
      this._guardar();
    },
    estaEnCarrito(productoId) {
      return this.items.some(item => item.id === productoId);
    },
    removeFromCart(productoId) {
      this.items = this.items.filter(item => item.id !== productoId);
      this._guardar();
    },
    clearCart() {
      this.items = [];
      this._guardar();
    },
    _guardar() {
      localStorage.setItem(CART_KEY, JSON.stringify(this.items));
    },
    async checkout() {
      try {
        const payload = {
          items: this.items.map(item => ({
            id_producto: item.id,
            precio_unitario: item.precio
          }))
        };
        await api.post('/compras', payload);
        this.clearCart();
        return true;
      } catch (error) {
        console.error('Error procesando compra con Laravel:', error);
        throw error;
      }
    }
  }
});
