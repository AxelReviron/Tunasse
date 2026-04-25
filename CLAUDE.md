# Tunasse

PWA de gestion de finances personnelles. Fonctionne entièrement hors-ligne — les données vivent uniquement sur le téléphone de l'utilisateur. Aucun serveur, aucune base distante.

---

## Stack

- **Vue 3** + Composition API (`<script setup>`)
- **Ionic Vue** — composants UI mobile
- **Dexie.js** — wrapper IndexedDB (base de données locale dans le navigateur)
- **vite-plugin-pwa** — Service Worker + Web App Manifest
- **Vue Router** — navigation
- Pas de Pinia pour l'instant — les composables gèrent la réactivité

---

## Architecture

```
src/
├── db/
│   └── database.ts          ← instance Dexie singleton, schema, migrations
├── repositories/             ← CRUD pur, seul endroit où Dexie est importé
│   ├── AccountRepository.ts
│   ├── TransactionRepository.ts
│   └── BudgetRepository.ts
├── services/                 ← logique métier (calculs, agrégats), sans dépendance Dexie
│   ├── AccountService.ts
│   ├── TransactionService.ts
│   └── BudgetService.ts
├── composables/              ← réactivité Vue, appellent les services
│   ├── useAccounts.ts
│   ├── useTransactions.ts
│   ├── useBudgets.ts
│   ├── useFormat.ts
│   └── useTransactionFilters.ts
├── components/
│   ├── ui/                   ← atoms Tns* (voir liste ci-dessous)
│   └── *.vue                 ← charts et blocs complexes
├── views/                    ← pages, consomment uniquement les composables
└── router/
    └── index.ts
```

### Règle de dépendance stricte

```
View → Composable → Service → Repository → Dexie
```

Chaque couche ne connaît que celle du dessous. Un composable n'importe jamais Dexie directement.

---

## Base de données — Dexie

### Définition du schema (`db/database.ts`)

Dans `.stores()`, seuls les **champs indexés** sont déclarés. Les autres propriétés de l'objet sont stockées automatiquement.

```ts
import Dexie, { type EntityTable } from 'dexie'
import type { Account, Transaction, Budget } from './types'

const db = new Dexie('TunasseDB') as Dexie & {
  accounts: EntityTable<Account, 'id'>
  transactions: EntityTable<Transaction, 'id'>
  budgets: EntityTable<Budget, 'id'>
}

db.version(1).stores({
  accounts:     '++id, type',
  transactions: '++id, account_id, type, date, budget_id, is_recurring',
  budgets:      '++id',
})

export { db }
```

### Migrations

- Toujours incrémenter la version pour tout changement de schema
- `.upgrade()` est optionnel — nécessaire uniquement pour migrer des données existantes
- Pas de rollback possible (limite d'IndexedDB) — corriger avec une version suivante
- En dev : effacer la base via DevTools navigateur si nécessaire

```ts
db.version(2).stores({
  transactions: '++id, account_id, type, date, budget_id, is_recurring, category'
}).upgrade(tx =>
  tx.table('transactions').toCollection().modify(t => {
    t.category = t.category ?? null
  })
)
```

---

## Types de données

```ts
type Account = {
  id: number
  label: string
  iban: string
  type: 'checking' | 'savings' | 'investment'
  balance: number        // solde initial — le solde réel est calculé depuis les transactions
  currency: 'EUR' | 'USD' | 'GBP' | 'BTC'
  color: string          // hex
}

type Budget = {
  id: number
  label: string
  amount: number         // plafond mensuel
  spent: number          // calculé, pas stocké directement
  currency: string
  color: string
  icon?: string
}

type Transaction = {
  id: number
  label: string
  amount: number         // toujours positif, le signe vient de `type`
  type: 'income' | 'expense'
  date: string           // ISO YYYY-MM-DD
  location?: string
  category?: string
  account_id: number
  budget_id?: number
  is_recurring?: boolean
  recurring_unit?: 'day' | 'week' | 'month' | 'year'
}
```

---

## Composants UI — préfixe `Tns*`

```
components/ui/
├── TnsLargeTitle.vue          ← titre de page iOS 34 px
├── TnsSectionHeader.vue       ← header de groupe en majuscules
├── TnsList.vue                ← conteneur arrondi pour les lignes
├── TnsKpiCard.vue             ← carte métrique avec tone (green/red/neutral)
├── TnsTransactionRow.vue      ← ligne de transaction
├── TnsAccountCard.vue         ← ligne page Comptes
├── TnsBudgetProgress.vue      ← barre de progression + méta
├── TnsFilterChips.vue         ← pills segmentées (v-model)
├── TnsBottomSheet.vue         ← sheet modale iOS
├── TnsFormField.vue           ← champ labellisé pour les sheets
├── TnsTypeToggle.vue          ← toggle income/expense
└── TnsAmountInput.vue         ← montant centré grand format
```

Préfixe `Tns` pour éviter les collisions avec les composants `Ion*` d'Ionic.

---

## Composables

### `useFormat()`
Tout le formatage monnaie/date passe par là — jamais d'appel `Intl.NumberFormat` inline.

```ts
const { fmt, fmtShort, fmtDay, fmtDateShort } = useFormat()
fmt(1234.5, 'EUR')        // "1 234,50 €"
fmtShort(1234.5, 'EUR')   // "1 235 €"
fmtDay('2026-04-24')      // "24 April"
fmtDateShort('2026-04-24') // "24 Apr"
```

### `useTransactionFilters(transactions)`
Prend un `Ref<Transaction[]>`, retourne `filter`, `query`, `filtered`, `grouped`.

```ts
const { filter, grouped } = useTransactionFilters(transactions)
// filter.value = 'all' | 'income' | 'expense' | 'recurring'
// grouped.value = { "24 April": [...], "23 April": [...] }
```

---

## Conventions

1. **`<script setup>` avant `<template>`** dans tous les fichiers Vue.
2. **Pas d'import Dexie hors des repositories.** Si tu as besoin de données dans un composable, passe par un repository.
3. **Toujours `useFormat()`** pour monnaie et dates.
4. **Icons = slots, pas props.** Passer via `<template #icon>` pour rester agnostique vis-à-vis des librairies d'icônes.
5. **CSS `scoped` partout**, avec `:deep()` pour les enfants slottés.
6. **Les vues ne font pas de logique.** Elles appellent des composables et affichent. La logique métier va dans les services.
7. **Les composants ne touchent pas à la base.** Tout remonte via events ou composables.
8. **Pas de Pinia pour l'instant.** Si un besoin de partage d'état global apparaît entre vues sans relation parent/enfant, évaluer Pinia à ce moment-là.

---

## Theming

Les tokens CSS sont dans `src/theme/variables.css`. À importer une seule fois à la racine.

- Accent global → surcharger `--tns-accent` sur `:root`
- Dark mode → `data-theme="dark"` sur `<html>`
- Densité compacte → `data-density="compact"` sur un conteneur parent
- Override local → `style="--tns-accent: #10B981;"` sur un sous-arbre

---

## PWA

`vite-plugin-pwa` gère le Service Worker (Workbox) et le Web App Manifest. Config dans `vite.config.ts`. L'app doit fonctionner entièrement offline — toutes les données sont en local via IndexedDB, l'app shell est mis en cache par le Service Worker.

Export / import via fichier JSON prévu plus tard — pas dans le scope actuel.
