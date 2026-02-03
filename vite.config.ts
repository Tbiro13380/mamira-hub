import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

// Verificar se estamos em modo de build de produção
// Em produção/build, desabilitar wayfinder completamente para evitar erros
const isProductionBuild = process.env.NODE_ENV === 'production' || process.env.CI === 'true';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        // Wayfinder apenas em desenvolvimento - desabilitado durante build de produção
        ...(!isProductionBuild
            ? [
                  wayfinder({
                      formVariants: true,
                      generateOnBuild: false,
                  }),
              ]
            : []),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ]
});
