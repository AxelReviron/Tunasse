<h1 align="center">Tunasse</h1>

<p align="center">
  <a href="https://vuejs.org"><img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white" alt="Vue.js"></a>
  <a href="https://ionicframework.com"><img src="https://img.shields.io/badge/Ionic-8-3880FF?style=flat-square&logo=ionic&logoColor=white" alt="Ionic"></a>
  <a href="https://dexie.org"><img src="https://img.shields.io/badge/Dexie.js-4-FF6B35?style=flat-square&logo=dexie&logoColor=white" alt="Dexie.js"></a>
  <a href="https://web.dev/progressive-web-apps"><img src="https://img.shields.io/badge/PWA-ready-5A0FC8?style=flat-square&logo=pwa&logoColor=white" alt="PWA"></a>
</p>


## Privacy first

Tunasse stores everything locally on your device using IndexedDB. There is no server, no account, no analytics, no cloud sync. Your financial data is yours — it never leaves your phone or browser.


## Features

- **Dashboard** — Balance overview, monthly income/expense, recurring bills tracker (already paid / still to pay), charts by account and budget
- **Transactions** — Income, expenses and transfers between accounts; search and filters by type, date range; recurring transactions
- **Budgets** — Monthly caps per category with progress tracking
- **Accounts** — Multiple accounts, multiple currencies, balance computed from transactions
- **Offline** — Fully functional without internet; installable as a PWA on any device


## Tech stack

| Layer          | Technology                                                                       |
|----------------|----------------------------------------------------------------------------------|
| UI framework   | [Ionic Vue](https://ionicframework.com/docs/vue/overview)                        |
| Reactivity     | [Vue 3](https://vuejs.org) Composition API (`<script setup>`)                    |
| Local database | [Dexie.js](https://dexie.org) (IndexedDB wrapper)                                |
| PWA            | [vite-plugin-pwa](https://vite-pwa-org.netlify.app) — Workbox + Web App Manifest |
| Routing        | [Vue Router](https://router.vuejs.org)                                           |
| Build          | [Vite](https://vitejs.dev) + [TypeScript](https://www.typescriptlang.org)        |
| i18n           | [Vue I18n](https://vue-i18n.intlify.dev) (English / French)                      |
| Charts         | [Chart.js](https://www.chartjs.org) via [vue-chartjs](https://vue-chartjs.org)   |


## Getting started

```bash
docker build -t tunasse-dev .
docker run --rm --name tunasse -p 8100:8100 -v $(pwd):/app -v /app/node_modules tunasse-dev
```
### Execute commands inside the container
```bash
docker exec -it tunasse sh

```
Build for production:

```bash
npm run build
```

## Roadmap

- [ ] Settings page — Import/Export, full data reset
- [ ] PWA icons & splash screen
- [ ] Onboarding
