/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: 'var(--color-primary)',

        background: 'var(--color-background)',
        surface: 'var(--color-surface)',
        border: 'var(--color-border)',

        text: 'var(--color-text)',
        error: 'var(--color-error)',
      }
    },
  },
  plugins: [],
}
