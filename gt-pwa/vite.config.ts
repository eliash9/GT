import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { VitePWA } from 'vite-plugin-pwa'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    VitePWA({
      registerType: 'autoUpdate',
      manifest: {
        name: 'SiGT Laporan',
        short_name: 'SiGT',
        description: 'Aplikasi Pelaporan GT, PJGT, dan Korwil',
        theme_color: '#22c55e',
        background_color: '#ffffff',
        display: 'standalone',
      }
    })
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    port: 5174,
  }
})
