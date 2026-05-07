import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { compression } from 'vite-plugin-compression2';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Pre-compress built assets at build time so nginx can serve
        // app-XYZ.css.gz / .br directly via gzip_static/brotli_static
        // without spending CPU on every request.
        compression({
            algorithm: 'gzip',
            threshold: 1024,
            deleteOriginalAssets: false,
        }),
        compression({
            algorithm: 'brotliCompress',
            threshold: 1024,
            deleteOriginalAssets: false,
        }),
    ],
});
