// vite.config.js
import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
  // Helpful when your PHP app is served from Apache (e.g. http://kabi-test.local)
  // while Vite runs on http://localhost:5173
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
    },
  },

  // SCSS resolver (lets you use `@use "abstracts/variables"` etc.)
  // NOTE: with includePaths you can do `@use "abstracts/variables" as *;`
  css: {
    preprocessorOptions: {
      scss: {
        includePaths: [path.resolve(__dirname, "resources/scss")],
      },
    },
  },

  build: {
    // Compiled output goes here (safe to wipe on each build)
    outDir: "public/build",
    emptyOutDir: true,

    rollupOptions: {
      // Two explicit inputs to keep stable output filenames (no hashes)
      input: {
        app: path.resolve(__dirname, "resources/js/app.js"),
        style: path.resolve(__dirname, "resources/scss/style.scss"),
      },
      output: {
        // Stable JS entry filename
        entryFileNames: (chunk) =>
          chunk.name === "app" ? "js/app.js" : "js/[name].js",
        chunkFileNames: "js/[name].js",

        // Stable CSS filename + other assets
        assetFileNames: (assetInfo) => {
          // Rollup v4+: use `names` (plural). `name` is deprecated.
          const names = assetInfo.names || [];

          if (names.some((n) => n.endsWith(".css"))) {
            return "css/style.css";
          }

          return "assets/[name][extname]";
        },
      },
    },
  },
});
