/** @type {import('tailwindcss').Config} */
module.exports = {
    mode: "jit",
    prefix: "tw-",
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    corePlugins: {
        preflight: false,
    },
    theme: {
        screens: {
            xs: "425px",
            sm: "640px",
            md: "768px",
            lg: "1024px",
            xl: "1280px",
            "2xl": "1536px",
        },
        extend: {},
    },
    plugins: [],
};
