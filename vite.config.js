import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
  server: {
    cors: true,
  },

  root: '.',
  build: {
    outDir: 'public/assets',
    emptyOutDir: false, // keep any other assets you might add later
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'resources/js/app.js'),
        style: path.resolve(__dirname, 'resources/scss/style.scss'),
      },
      output: {
        entryFileNames: (chunk) => {
          // Place JS entry in /js
          return chunk.name === 'app' ? 'js/app.js' : 'js/[name].js';
        },
        chunkFileNames: 'js/[name].js',
        assetFileNames: (assetInfo) => {
          // Place CSS in /css and keep stable names
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/style.css';
          }
          return 'assets/[name][extname]';
        },
      },
    },
  },
});
