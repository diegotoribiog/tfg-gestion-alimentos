import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                midnight: {
                    DEFAULT: '#0f172a',
                    dark: '#0f172a',
                    card: '#1e293b',
                },
                emerald: {
                    vibrant: '#10b981',
                },
                slate: {
                    base: '#f8fafc',
                }
            },
        },
    },

    plugins: [forms],
};
