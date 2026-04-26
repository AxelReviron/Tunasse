import type { Currency } from '@/types'

export const CURRENCY_SUBUNIT: Record<Currency, number> = {
  EUR: 100,
  USD: 100,
  GBP: 100,
  BTC: 100_000_000,
}

export function toSubunits(input: string, subunit: number): number {
  const decimals = Math.log10(subunit)
  const [intPart, decPart = ''] = input.replace(',', '.').split('.')
  const paddedDec = decPart.padEnd(decimals, '0').slice(0, decimals)
  return parseInt(intPart + paddedDec, 10)
}
