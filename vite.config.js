export default defineConfig({
    base: '/build/',  // Cambiar de '/public/build/' a '/build/'
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
});