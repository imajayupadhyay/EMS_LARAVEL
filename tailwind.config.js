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
    },

    plugins: [forms],
    extend: {
  colors: {
    primary: {
      light: '#f97316',
      DEFAULT: '#ea580c',
      dark: '#c2410c',
    },
  },
  backgroundImage: {
    'gradient-orange': 'linear-gradient(to right, #f97316, #ea580c)',
  },
},

};
