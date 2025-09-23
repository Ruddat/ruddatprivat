import defaultTheme from 'tailwindcss/defaultTheme'

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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    darkMode: 'class',
    plugins: [
        require("daisyui"),
    require('@tailwindcss/typography')   
    ],
        daisyui: {
        themes: [
            {
                ruddattech: {
                    "primary": "#ec4899",   // Pink
                    "secondary": "#f97316", // Orange
                    "accent": "#8b5cf6",    // Lila
                    "neutral": "#3d4451",
                    "base-100": "#ffffff",  // Hintergrund Weiß
                    "base-200": "#f9fafb",  // Hellgrau (Abschnitte)
                    "base-300": "#d1d5db",
                    "info": "#3b82f6",
                    "success": "#22c55e",
                    "warning": "#facc15",
                    "error": "#ef4444",
                },
            },
        ],
    },
}
