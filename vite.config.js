import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload';
import copy from 'rollup-plugin-copy'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: 
  [
    vue(),
  ],

  build: {
    manifest: true,
    outDir: 'assets',
    assetsDir: 'assetsDIR',
    emptyOutDir: true, // delete the contents of the output directory before each build

    rollupOptions: {
      input: [
        'resources/js/app.js',
      ],
      output: {
        chunkFileNames: 'js/[name].js',
        entryFileNames: 'js/[name].js',
        
        assetFileNames: ({name}) => {
          
          if (/\.css$/.test(name ?? '')) {
              return 'css/[name]-[hash][extname]';   
          }
          return '[name]-[hash][extname]';
        },
      },
    },
  },

  resolve: {
    alias: {
      'vue': 'vue/dist/vue.esm-bundler.js',
    },
  },

  server: {
    port: 8888,
    strictPort: true,
    hmr: {
      port: 8888,
      host: 'localhost',
      protocol: 'ws',
    }
  }
})