import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Http/Livewire/**/*.php',
    ],
    safelist: [
        'status-pending',
        'bg-red-400',
        'border-red-400',
        'bg-amber-400',
        'border-amber-400',
        'bg-emerald-500',
        'border-emerald-500',
        'bg-gray-400',
        'border-gray-400',
    ],
    darkMode: 'selector',
    theme: {
        extend: {
            fontFamily: {
                brand: [
                    'Montserrat',
                    'Helvetica Neue',
                    'Helvetica',
                    'Arial',
                    'sans-serif',
                ],
                sans: [
                    'Montserrat',
                    'Helvetica Neue',
                    'Helvetica',
                    'Arial',
                    'sans-serif',
                ],
                mono: ['IBM Plex Mono', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace'],
            },
            colors: {
                // Íntegro brand palette
                'integro-black': '#000000',
                'integro-gray': '#89888a',
                'integro-light-gray': '#e1e2e4',
                'integro-red': '#c3302d',
                'integro-red-hover': '#ad2a28',
                'integro-red-active': '#982523',
                'integro-gray-text': '#747376',
                'integro-white': '#ffffff',

                // Semantic surfaces
                surface: 'var(--surface)',
                'surface-inverse': 'var(--surface-inverse)',
                'surface-muted': 'var(--surface-muted)',
                'surface-secondary': 'var(--surface-secondary)',

                // Semantic text
                'text-primary': 'var(--text-primary)',
                'text-inverse': 'var(--text-inverse)',
                'text-muted': 'var(--text-muted)',

                // Legacy Jetstream mapping (so existing text-gray-* etc. still work)
                // Override grays to match brand tone
                gray: {
                    50: '#f8f8f8',
                    100: '#f0f0f1',
                    200: '#e1e2e4',
                    300: '#c8c8cb',
                    400: '#a7a7aa',
                    500: '#89888a',
                    600: '#747376',
                    700: '#5c5c5e',
                    800: '#2d2d2e',
                    900: '#1a1a1a',
                    950: '#0d0d0d',
                },
            },
            borderRadius: {
                DEFAULT: '0.25rem',
                control: '0.25rem',
                surface: '0.25rem',
                pill: '999px',
                lg: '0.375rem',
                xl: '0.5rem',
            },
            boxShadow: {
                'card': '0 1px 2px 0 rgb(0 0 0 / 0.04), 0 2px 4px -1px rgb(0 0 0 / 0.04)',
                'elevated': '0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04)',
                'dialog': '0 20px 25px -5px rgb(0 0 0 / 0.12), 0 8px 10px -6px rgb(0 0 0 / 0.08)',
                'soft': '0 1px 2px 0 rgb(0 0 0 / 0.03), 0 1px 3px 0 rgb(0 0 0 / 0.03)',
            },
            transitionTimingFunction: {
                'brand': 'cubic-bezier(0.16, 1, 0.3, 1)',
            },
            keyframes: {
                'accordion-down': {
                    from: { height: '0' },
                    to: { height: 'var(--radix-accordion-content-height)' },
                },
                'accordion-up': {
                    from: { height: 'var(--radix-accordion-content-height)' },
                    to: { height: '0' },
                },
            },
            animation: {
                'accordion-down': 'accordion-down 0.2s ease-out',
                'accordion-up': 'accordion-up 0.2s ease-out',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
