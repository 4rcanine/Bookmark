// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    // Remove 'dark' mode if you want to strictly follow the light theme image
    // darkMode: 'class', // Keep if you want to attempt a dark version later

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans], // Keep default or choose a 'cozier' font like 'Nunito'
            },
            // Define cozy theme colors based on the target image
            colors: {
                'cozy-cream': '#FFFBEB', // Light creamy background for content
                'cozy-brown': {
                    light: '#D2B48C', // Lighter brown for borders/accents
                    DEFAULT: '#8B4513', // Main brown for text/borders
                    dark: '#5a2d0c',  // Darker brown for text
                },
                'cozy-green': {
                    light: '#D4EDDA', // Light green for success messages
                    DEFAULT: '#90EE90', // A generic light green
                    dark: '#2E8B57',  // Darker green for links maybe
                },
                'cozy-purple': {
                    light: '#E9D5FF',
                    DEFAULT: '#8A2BE2', // Purple for buttons like the example
                    dark: '#6A1B9A',
                },
                'cozy-text': {
                    DEFAULT: '#5a2d0c', // Dark brown default text
                    muted: '#A0522D',   // Softer brown for muted text
                }
            },
            // Define the background image
            backgroundImage: {
              'cozy-cat': "url('/images/cozy-background.png')",
            }
        },
    },

    plugins: [forms, typography],
};