/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/**/*.{ts,tsx}', './components/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        gold:    '#C4973A',
        'gold-hover': '#B8892E',
        sidebar: '#1A1A1A',
        ce:      '#0A0A0A',
        muted:   '#666666',
        border:  '#E8E8E8',
        surface: '#FFFFFF',
        bg:      '#FAFAFA',
      },
      fontFamily: {
        sans:    ['Inter', 'sans-serif'],
        display: ['"Playfair Display"', 'serif'],
      },
    },
  },
  plugins: [],
};
