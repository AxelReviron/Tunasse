import { createI18n } from 'vue-i18n'
import fr from '@/locales/fr'
import en from '@/locales/en'

type MessageSchema = typeof fr

function detectLocale(): 'fr' | 'en' {
  const lang = (navigator.languages?.[0] ?? navigator.language ?? 'en').toLowerCase()
  return lang.startsWith('fr') ? 'fr' : 'en'
}

export const i18n = createI18n<[MessageSchema], 'fr' | 'en'>({
  legacy: false,
  locale: detectLocale(),
  fallbackLocale: 'en',
  messages: { fr, en },
})
