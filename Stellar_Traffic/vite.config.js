import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [tailwindcss(), laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true
  }), vue({
    template: {
      transformAssetUrls: {
        base: null,
        includeAbsolute: false
      }
    }
  })],
  css: {
    preprocessorOptions: {
      scss: {
        quietDeps: true
      }
    }
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js'
    }
  },
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**']
    }
  }
});