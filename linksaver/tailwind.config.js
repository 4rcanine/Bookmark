import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    

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
                'cozy-cream': '#FFFBEB', 
                'cozy-brown': {
                    light: '#D2B48C', 
                    DEFAULT: '#8B4513',
                    dark: '#5a2d0c', 
                },
                'cozy-green': {
                    light: '#D4EDDA', 
                    DEFAULT: '#90EE90', 
                    dark: '#2E8B57', 
                },
                'cozy-purple': {
                    light: '#E9D5FF',
                    DEFAULT: '#8A2BE2', 
                    dark: '#6A1B9A',
                },
                'cozy-text': {
                    DEFAULT: '#5a2d0c',
                    muted: '#A0522D',   
                }
            },
            backgroundImage: {
              'cozy-cat': "url('/images/cozy-background.png')",
            }
        },
    },

    plugins: [forms, typography],
};