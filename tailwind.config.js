import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#eef6ff",
                    100: "#d9ebff",
                    200: "#bcdeff",
                    300: "#8eccff",
                    400: "#59b0ff",
                    500: "#0066FF", // Core SayaBantu Brand Blue
                    600: "#0052cc", // Active / Hover
                    700: "#003da5", // Deep brand blue
                    800: "#003285",
                    900: "#002b6d",
                    950: "#001a45",
                },
                brand: {
                    50: "#eef6ff",
                    100: "#d9ebff",
                    200: "#bcdeff",
                    300: "#8eccff",
                    400: "#59b0ff",
                    500: "#0066FF",
                    600: "#0052cc",
                    700: "#003da5",
                    800: "#003285",
                    900: "#002b6d",
                },
                emerald: {
                    50: "#ecfdf5",
                    100: "#d1fae5",
                    200: "#a7f3d0",
                    300: "#6ee7b7",
                    400: "#34d399",
                    500: "#10b981",
                    600: "#059669",
                    700: "#047857",
                    800: "#065f46",
                    900: "#064e3b",
                },
            },
            maxWidth: {
                mobile: "420px",
            },
        },
    },

    plugins: [forms],
};
