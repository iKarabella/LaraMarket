import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */

export default {
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
        },
        fontSize: {
            xs: ['.75rem', '1rem'],
            sm: ['.875rem', '1.25rem'],
            base: ['1rem', '1.5rem'],
            lg: ['1.125rem', '1.75rem'],
            xl: ['1.25rem', '1.75rem'],
            '2xl':	['1.5rem',  '2rem'],
            '3xl':	['1.875rem', '2.25rem'],
            '4xl': ['2.25rem', '2.5rem'],
            '5xl': ['2.75rem', '3rem'],
            '6xl':	['3.75rem']
        }
    },

    plugins: [forms],
};