import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    // Optimizaciones para producción
    target: 'es2020',
    minify: 'esbuild',  // Usar esbuild en lugar de terser (más rápido y sin dependencias)
    // Configuración de chunks
    rollupOptions: {
      output: {
        manualChunks: {
          'react-vendor': ['react', 'react-dom', 'react-router-dom'],
          'three-vendor': ['three'],
          'ui-vendor': ['lucide-react'],
        },
        // Nombre de archivos con hash para cache busting
        entryFileNames: 'js/[name].[hash].js',
        chunkFileNames: 'js/[name].[hash].js',
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const ext = info[info.length - 1];
          if (/png|jpe?g|gif|svg|webp|ico/.test(ext)) {
            return `images/[name].[hash][extname]`;
          } else if (/woff|woff2|ttf|otf|eot/.test(ext)) {
            return `fonts/[name].[hash][extname]`;
          } else if (ext === 'css') {
            return `css/[name].[hash][extname]`;
          }
          return `[name].[hash][extname]`;
        },
      },
    },
    // Límites de tamaño
    reportCompressedSize: true,
    chunkSizeWarningLimit: 500,
    // Ubicación del build
    outDir: 'dist',
    assetsDir: 'assets',
    emptyOutDir: true,
    // Source maps solo en desarrollo
    sourcemap: false,
  },
  server: {
    host: 'localhost',
    port: 5173,
    open: false,
  },
})
