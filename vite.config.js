import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
                // Bawaan plugin hanya subset latin; tanpa 'arabic' huruf Arab jatuh ke font sistem.
                bunny('Amiri', {
                    weights: [400],
                    subsets: ['arabic', 'latin'],
                }),
                bunny('Amiri Quran', {
                    weights: [400],
                    subsets: ['arabic', 'latin'],
                }),
                bunny('Cormorant Garamond', {
                    weights: [500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
