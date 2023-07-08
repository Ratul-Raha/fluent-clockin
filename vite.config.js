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
    // publicDir: 'public',
    emptyOutDir: true, // delete the contents of the output directory before each build

    rollupOptions: {
      input: [
        'resources/js/app.js',
      ],
      output: {
        // chunkFileNames: 'js/[name]-[hash].js',
        // entryFileNames: 'js/[name]-[hash].js',
        chunkFileNames: 'js/[name].js',
        entryFileNames: 'js/[name].js',
        
        assetFileNames: ({name}) => {
          // if (/\.(gif|jpe?g|png|svg)$/.test(name ?? '')){
          //     return 'images/[name]-[hash][extname]';
          // }
          
          if (/\.css$/.test(name ?? '')) {
              return 'css/[name]-[hash][extname]';   
          }
 
          // default value
          // ref: https://rollupjs.org/guide/en/#outputassetfilenames
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