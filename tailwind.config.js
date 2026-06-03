/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                // Kita simpan warna khas SILAMPU di sini agar mudah dipanggil
                primary: '#0B192C',
                secondary: '#3B82F6',
                accent: '#F59E0B',
            }
        },
    },
    plugins: [],
}