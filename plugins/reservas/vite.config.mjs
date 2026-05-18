import { v4wp } from '@kucrut/vite-for-wp';
import { wp_scripts } from '@kucrut/vite-for-wp/plugins';
import tailwindcss from '@tailwindcss/vite'

export default {
  plugins: [
    tailwindcss(),
    v4wp({
      input: 'assets/js/main.js', // Optional, defaults to 'src/main.js'.
    }),
    wp_scripts(),
    {
      name: 'override-config',
      config: () => ({
        build: {
          // ensure that manifest.json is not in ".vite/" folder
          manifest: 'manifest.json',
        },
      }),
    }

  ],
  server: {
    host: 'aba.lndo.site',
    cors: {
      origin: 'http://aba.lndo.site',
      methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
      allowedHeaders: ['Content-Type', 'Authorization'],
    },
  },
};