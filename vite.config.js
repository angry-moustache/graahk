import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import mkcert from 'vite-plugin-mkcert'

export default defineConfig({
  server: {
    https: true,
    host: '0.0.0.0',
    hmr: {
      host: '192.168.0.131'
    },
  },
  plugins: [
    mkcert(),
    laravel({
      input: [
        'resources/css/app.scss',
        'resources/js/app.js'
      ],
    }),
  ],
});
