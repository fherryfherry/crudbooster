/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './src/Livewire/**/*.blade.php',
        './src/Themes/**/*.blade.php',
        './src/Modules/**/*.blade.php',
        './src/Components/**/*.blade.php',
        './src/Stubs/**/*.blade.php.stub',
    ],
    safelist: ['w-10', 'h-10'],
    theme: {
        screens: {
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
          },
        extend: {},
    },
    plugins: [
        require('tailwindcss-rtl'),
        // other plugins...
    ],
}

