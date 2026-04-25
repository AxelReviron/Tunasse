// useFormat — centralised number / currency / date formatting.
// Keep all locale logic here so currency + date formats stay consistent app-wide.

const CURRENCY_LOCALES = {
  EUR: 'fr-FR',
  USD: 'en-US',
  GBP: 'en-GB',
  BTC: 'en-US',
};

export function useFormat() {
  /** Format a signed amount with currency symbol.
   *  fmt(1234.5, 'EUR') -> "1 234,50 €"
   *  BTC gets 4 fractional digits. */
  function fmt(amount, currency = 'EUR') {
    if (currency === 'BTC') {
      return `${Number(amount).toFixed(4)} ₿`;
    }
    return new Intl.NumberFormat(CURRENCY_LOCALES[currency] || 'fr-FR', {
      style: 'currency',
      currency,
      maximumFractionDigits: 2,
    }).format(amount);
  }

  /** Short version — strips decimals for amounts ≥ 1000. */
  function fmtShort(amount, currency = 'EUR') {
    if (currency === 'BTC') return `${Number(amount).toFixed(4)} ₿`;
    const abs = Math.abs(amount);
    const opts = abs >= 1000 ? { maximumFractionDigits: 0 } : { maximumFractionDigits: 2 };
    return new Intl.NumberFormat(CURRENCY_LOCALES[currency] || 'fr-FR', {
      style: 'currency', currency, ...opts,
    }).format(amount);
  }

  /** Date label used in grouped transaction lists — "24 April". */
  function fmtDay(date) {
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long' });
  }

  /** Short date — "24 Apr". */
  function fmtDateShort(date) {
    return new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
  }

  return { fmt, fmtShort, fmtDay, fmtDateShort };
}
