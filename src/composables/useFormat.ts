import { useI18n } from 'vue-i18n'
import type { Currency } from '@/types'
import { CURRENCY_SUBUNIT } from '@/constants/currencies'

const CURRENCY_LOCALES: Record<Currency, string> = {
  EUR: 'fr-FR',
  USD: 'en-US',
  GBP: 'en-GB',
  BTC: 'en-US',
}

const DATE_LOCALES: Record<string, string> = {
  fr: 'fr-FR',
  en: 'en-GB',
}

export function useFormat() {
  const { locale } = useI18n()

  function dateLocale(): string {
    return DATE_LOCALES[locale.value] ?? 'fr-FR'
  }

  function fmt(amountInSubunits: number, currency: Currency = 'EUR'): string {
    const amount = amountInSubunits / CURRENCY_SUBUNIT[currency]
    if (currency === 'BTC') return `${amount.toFixed(8)} ₿`
    return new Intl.NumberFormat(CURRENCY_LOCALES[currency], {
      style: 'currency',
      currency,
      maximumFractionDigits: 2,
    }).format(amount)
  }

  function fmtShort(amountInSubunits: number, currency: Currency = 'EUR'): string {
    const amount = amountInSubunits / CURRENCY_SUBUNIT[currency]
    if (currency === 'BTC') return `${amount.toFixed(4)} ₿`
    const opts: Intl.NumberFormatOptions = Math.abs(amount) >= 1000
      ? { maximumFractionDigits: 0 }
      : { maximumFractionDigits: 2 }
    return new Intl.NumberFormat(CURRENCY_LOCALES[currency], {
      style: 'currency', currency, ...opts,
    }).format(amount)
  }

  function fmtDay(date: string): string {
    return new Date(date).toLocaleDateString(dateLocale(), { day: 'numeric', month: 'long' })
  }

  function fmtDateShort(date: string): string {
    return new Date(date).toLocaleDateString(dateLocale(), { day: '2-digit', month: 'short' })
  }

  return { fmt, fmtShort, fmtDay, fmtDateShort }
}
