import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  preview: {
    port: process.env.PORT ? parseInt(process.env.PORT) : 4173,
    host: true,
    allowedHosts: 'all',
  },
})
