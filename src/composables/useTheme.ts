import { ref, watch } from 'vue'

export const ACCENT_PRESETS = [
  { key: 'indigo', value: '#6366F1' },
  { key: 'purple', value: '#8B5CF6' },
  { key: 'blue',   value: '#3B82F6' },
  { key: 'teal',   value: '#14B8A6' },
  { key: 'green',  value: '#22C55E' },
  { key: 'orange', value: '#F97316' },
  { key: 'pink',   value: '#EC4899' },
] as const

type Theme = 'dark' | 'light'

const theme  = ref<Theme>((localStorage.getItem('tns-theme') as Theme) ?? 'dark')
const accent = ref<string>(localStorage.getItem('tns-accent') ?? '#6366F1')

function applyTheme(t: Theme, a: string) {
  document.documentElement.setAttribute('data-theme', t)
  document.documentElement.style.setProperty('--tns-accent', a)
  localStorage.setItem('tns-theme', t)
  localStorage.setItem('tns-accent', a)
}

watch([theme, accent], ([t, a]) => applyTheme(t, a))

export function useTheme() {
  return { theme, accent, ACCENT_PRESETS }
}
