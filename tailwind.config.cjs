/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './index.html',
        './src/**/*.{js,jsx,ts,tsx}',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#00e9fa',
                secondary: '#333436',
                background: '#000000',
                'white-text': '#FFFFFF',
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                mono: ['Roboto Mono', 'monospace'],
            },
            borderRadius: {
                'xl-sm': '2px',
            },
        },
    },
    plugins: [],
};