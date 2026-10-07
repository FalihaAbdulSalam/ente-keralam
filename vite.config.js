import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.jsx',
            ],
            refresh: true,
        }),
        react({
            jsxRuntime: 'automatic',
        }),
    ],
    css: {
        // Avoid noisy warnings for missing third-party CSS source maps in dev
        devSourcemap: false,
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Core React libraries
                    'vendor-react': ['react', 'react-dom', 'react-router-dom'],
                    // UI libraries
                    'vendor-ui': ['react-bootstrap', 'bootstrap', 'react-icons'],
                    // Carousel/slider libraries
                    'vendor-carousel': ['react-slick', 'swiper'],
                    // PDF/Canvas libraries (lazy loaded when needed)
                    'vendor-pdf': ['jspdf', 'html2canvas'],
                    // Other utilities
                    'vendor-utils': ['axios', 'react-toastify', 'react-select', 'canvas-confetti'],
                },
            },
        },
        // Increase chunk size warning limit to reduce noise
        chunkSizeWarningLimit: 600,
    },
});
