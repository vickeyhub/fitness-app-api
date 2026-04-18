import defaultTheme from 'tailwindcss/defaultTheme';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                fx: {
                    950: '#030712',
                    900: '#0a0f1a',
                    850: '#111827',
                    800: '#1e293b',
                    accent: '#2dd4bf',
                    'accent-deep': '#0d9488',
                    lime: '#bef264',
                    coral: '#fb7185',
                },
            },
            fontFamily: {
                sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'fx-glow': '0 0 60px -10px rgba(45, 212, 191, 0.45)',
                'fx-card': '0 25px 50px -12px rgba(0, 0, 0, 0.55)',
                'fx-soft': '0 4px 24px rgba(0, 0, 0, 0.35)',
            },
            animation: {
                'fx-float': 'fx-float 6s ease-in-out infinite',
                'fx-pulse-slow': 'fx-pulse-slow 4s ease-in-out infinite',
            },
            keyframes: {
                'fx-float': {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-8px)' },
                },
                'fx-pulse-slow': {
                    '0%, 100%': { opacity: '0.4' },
                    '50%': { opacity: '0.8' },
                },
            },
        },
    },
    plugins: [daisyui],
    daisyui: {
        themes: ['light', 'dark'],
        darkTheme: 'dark',
    },
};
