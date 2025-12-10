/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js"
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Matte Green Theme
        primary: {
          50: '#f0f7f4',
          100: '#dceee5',
          200: '#b9ddcb',
          300: '#8bc5aa',
          400: '#5fa987',
          500: '#4A6F52', // Main matte green
          600: '#3d5a43',
          700: '#334937',
          800: '#2b3a2d',
          900: '#243127',
        },
        accent: {
          50: '#f5f9f7',
          100: '#e8f2ed',
          200: '#d1e5db',
          300: '#a7cdb9',
          400: '#76b095',
          500: '#5a9275', // Lighter green for hover states
          600: '#4a7a61',
          700: '#3d6250',
          800: '#344f42',
          900: '#2d4337',
        }
      },
      fontFamily: {
        'poppins': ['Poppins', 'sans-serif'],
        'inter': ['Inter', 'sans-serif'],
      },
      animation: {
        'float': 'float 6s ease-in-out infinite',
        'slide-in': 'slideIn 0.3s ease-out',
        'fade-in': 'fadeIn 0.5s ease-in',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-20px)' },
        },
        slideIn: {
          '0%': { transform: 'translateX(-100%)' },
          '100%': { transform: 'translateX(0)' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        }
      }
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
