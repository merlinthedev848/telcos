import forms from '@tailwindcss/forms'

export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Modules/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        ink: '#111827',
        line: '#d7dee8',
        panel: '#f8fafc',
        action: '#126a71',
        warn: '#b45309',
        danger: '#b91c1c'
      }
    }
  },
  plugins: [
    forms
  ]
}
