/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: 'var(--color-primary)',
        'primary-hover': 'var(--color-primary-hover)',

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
