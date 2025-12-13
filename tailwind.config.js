/** @type {import('tailwindcss').Config} */
export default {
  // Wajib ada: Mode class agar kita bisa kontrol via HTML class
  darkMode: 'class', 

  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'], 
      },
      colors: {
        'dinkes-primary': '#059669', 
        'dinkes-dark': '#064e3b',
        'dinkes-light': '#d1fae5',
      }
    },
  },
  plugins: [],
}