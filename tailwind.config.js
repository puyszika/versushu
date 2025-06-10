import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',

    ],

    theme: {
        extend: {
            filter: {
                none: 'none',
                grayscale: 'grayscale(1)',
            },
            animation: {
                'fade-in': 'fadeIn 0.8s ease-out',
                'slide-up': 'slideUp 0.6s ease-out'
            },
            keyframes: {
                fadeIn: {
                    '0%': {opacity: 0},
                    '100%': {opacity: 1},
                },
                slideUp: {
                    '0%': {opacity: 0, transform: 'translateY(20px)'},
                    '100%': {opacity: 1, transform: 'translateY(0)'}
                },
                fontFamily: {
                    sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                },

            },
        },

        plugins: [forms],
    }
};

