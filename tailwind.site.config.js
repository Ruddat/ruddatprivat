import defaultTheme from 'tailwindcss/defaultTheme'

/**
 * Eigene, schlanke Tailwind-Konfiguration für die öffentliche RuddatTech-Webseite.
 * Bewusst ohne DaisyUI/Flowbite, damit das Frontend-CSS klein bleibt.
 * Der Admin- und Kundenbereich nutzt weiterhin tailwind.config.js.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    content: [
        './resources/views/site/**/*.blade.php',
        './resources/js/site.js',
    ],
    theme: {
        extend: {
            colors: {
                navy: {
                    950: '#060C18',
                    900: '#0B1426',
                    800: '#111D35',
                    700: '#1A2A4A',
                },
                anthracite: {
                    900: '#151B24',
                    800: '#1F2937',
                    700: '#374151',
                },
                brass: {
                    50: '#FAF6EE',
                    100: '#F2E9D6',
                    200: '#E5D3AE',
                    300: '#D7BE8C',
                    400: '#C9A96E',
                    500: '#B8955A',
                    600: '#9A7A44',
                    700: '#7A5F35',
                },
            },
            fontFamily: {
                sans: ['"Inter"', 'ui-sans-serif', 'system-ui', '-apple-system', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', 'sans-serif'],
                mono: defaultTheme.fontFamily.mono,
            },
            maxWidth: {
                prose: '68ch',
            },
        },
    },
    plugins: [],
}
