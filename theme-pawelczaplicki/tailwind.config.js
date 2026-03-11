/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./template-*.php",
    "./**/*.php",
    "./assets/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        accent: "#1a1aff",
      },
      fontFamily: {
        sans: ["Mona Sans", "system-ui", "-apple-system", "BlinkMacSystemFont", "\"Segoe UI\"", "sans-serif"],
      },
      maxWidth: {
        content: "800px",
      },
    },
  },
  plugins: [],
};

