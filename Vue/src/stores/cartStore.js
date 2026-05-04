import { defineStore } from 'pinia';
import api from '../services/api';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
  }),
  getters: {
    totalItems: (state) => state.items.reduce((total, item) => total + item.cantidad, 0),
    totalPrice: (state) => state.items.reduce((total, item) => total + (item.precio * item.cantidad), 0),
  },
  actions: {
    addToCart(producto) {
      const existingItem = this.items.find(item => item.id === producto.id);
      if (existingItem) {
        existingItem.cantidad++;
      } else {
        this.items.push({ ...producto, cantidad: 1 });
      }
    },
    removeFromCart(productoId) {
      this.items = this.items.filter(item => item.id !== productoId);
    },
    clearCart() {
      this.items = [];
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
