import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    important: '.updates-app',
    content: [
        './components/**/*.{vue}',
    ],
    plugins: [forms],
    corePlugins: {
        preflight: false,
    },
    fontFamily: {
        sans: ['Rubik'],
    },
};
