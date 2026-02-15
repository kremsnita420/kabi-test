// vite.config.js
import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
  server: {
    host: "localhost",
    port: 5173,
    strictPort: true,
    cors: true,
    origin: "http://localhost:5173",
    hmr: {
      host: "localhost",
      port: 5173,
      protocol: "ws",
    },
  },

  // JS/SCSS aliases (lets you import without ./ or ../)
  resolve: {
    alias: {
      "@js": path.resolve(__dirname, "resources/js"),
      "@scss": path.resolve(__dirname, "resources/scss"),
      "@img": path.resolve(__dirname, "public/assets"), // optional convenience
    },
  },

  // SCSS resolver (lets you use `@use "abstracts/variables"` etc.)
  css: {
    preprocessorOptions: {
      scss: {
        includePaths: [path.resolve(__dirname, "resources/scss")],
      },
    },
  },

  build: {
    outDir: "public/build",
    emptyOutDir: true,

    rollupOptions: {
      input: {
        app: path.resolve(__dirname, "resources/js/app.js"),
        style: path.resolve(__dirname, "resources/scss/style.scss"),
        "admin-upload-hub": path.resolve(__dirname, "resources/js/pages/admin-upload-hub.js"),

      },

      output: {
        // Stable JS entry filenames (explicit mapping)
        entryFileNames: (chunk) => {
          if (chunk.name === "app") return "js/app.js";
          if (chunk.name === "admin-upload-hub") return "js/admin-upload-hub.js";
          return "js/[name].js";
        },

        chunkFileNames: "js/[name].js",

        // Stable CSS + assets
        assetFileNames: (assetInfo) => {
          const names = assetInfo.names || [];

          // All CSS into one predictable file
          if (names.some((n) => n.endsWith(".css"))) {
            return "css/style.css";
          }

          return "assets/[name][extname]";
        },
      },
    },
  },
});
