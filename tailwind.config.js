module.exports = {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/css/**/*.css",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#018790",
                    dark: "#005461",
                },
                accent: "#00b7b5",
                danger: {
                    DEFAULT: "#dc2626", // Tailwind's red-600
                    light: "#fee2e2", // Tailwind's red-100
                    dark: "#b91c1c", // Tailwind's red-700
                },
            },
        },
    },
    plugins: [],
};
