import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import path from "path";
import fs from 'fs';

export default defineConfig(({ mode }) => {
    const envDir = ".";

    Object.assign(process.env, loadEnv(mode, envDir));

    return {
        build: {
            emptyOutDir: true,
        },

        envDir,

        define: {
            global: 'globalThis',
        },

        server: {
            host: true,
            hmr: {
                host: process.env.VITE_HOST,
                clientPort: 443,
                protocol: 'wss',
            },
        },

        plugins: [
            tailwindcss(),
            laravel({
                hotFile: "public/hot",
                publicDirectory: "public",
                buildDirectory: "build",
                input: [
                    "resources/css/app.css",
                    "resources/js/app.js",
                ],
                refresh: true,
            }),
        ],

        experimental: {
            renderBuiltUrl(filename, { hostId, hostType, type }) {
                if (hostType === "css") {
                    return path.basename(filename);
                }
            },
        },
    };
});

// Clear hot files after build/dev process ends
const cleanup = () => {
    const hotFile = "public/hot";
    if (fs.existsSync(hotFile)) {
        fs.unlinkSync(hotFile);
        console.log('Hot file cleaned up');
    }
};

process.on('SIGINT', cleanup);  // Ctrl+C
process.on('SIGTERM', cleanup); // Kill command
process.on('exit', cleanup);    // Normal exit
