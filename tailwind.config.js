const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'vm-navy': {
                    DEFAULT: '#0A1647',
                    50: '#E8EBF5',
                    100: '#D1D7EB',
                    200: '#A3AFD7',
                    300: '#7587C3',
                    400: '#475FAF',
                    500: '#1A379B',
                    600: '#142C7C',
                    700: '#0F215D',
                    800: '#0A1647',
                    900: '#050B24',
                },
                'vm-gold': {
                    DEFAULT: '#D4AF37',
                    50: '#FBF8EC',
                    100: '#F7F1D9',
                    200: '#EFE3B3',
                    300: '#E7D58D',
                    400: '#DFC267',
                    500: '#D4AF37',
                    600: '#B89627',
                    700: '#8B711D',
                    800: '#5E4C13',
                    900: '#312709',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
