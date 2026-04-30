<h1 align="center">Tunasse</h1>

<p align="center">
  <a href="https://vuejs.org"><img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white" alt="Vue.js"></a>
  <a href="https://ionicframework.com"><img src="https://img.shields.io/badge/Ionic-8-3880FF?style=flat-square&logo=ionic&logoColor=white" alt="Ionic"></a>
  <a href="https://dexie.org"><img src="https://img.shields.io/badge/Dexie.js-4-FF6B35?style=flat-square&logo=dexie&logoColor=white" alt="Dexie.js"></a>
  <a href="https://web.dev/progressive-web-apps"><img src="https://img.shields.io/badge/PWA-ready-5A0FC8?style=flat-square&logo=pwa&logoColor=white" alt="PWA"></a>
</p>


## Privacy first

Tunasse stores everything locally on your device using IndexedDB. There is no account, no analytics, and no cloud where your data is held. Your financial data is yours — it lives on your devices.

Optional **device sync** is peer-to-peer over WebRTC, end-to-end encrypted by DTLS/SRTP. Peers find each other through a shared passphrase you control. No server ever stores or sees your data in the clear. See [Device sync](#device-sync) for the trade-offs.


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
| Device sync    | [Trystero](https://github.com/dmotz/trystero) (WebRTC, peer-to-peer)             |


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

## Device sync

Sync between your own devices is peer-to-peer over WebRTC, built on [Trystero](https://github.com/dmotz/trystero). Peers that share the same passphrase join the same room and exchange data directly.

**What stays private**

- All payload data is encrypted end-to-end by the browser's WebRTC stack (DTLS for the data channel, SRTP for any media). Relays in the path see only opaque ciphertext.
- The signaling layer (used to discover peers and exchange ICE candidates) carries no user data — only short connection-setup metadata.

**Network path**

WebRTC tries, in order:

1. **Direct LAN** — preferred whenever both peers are on the same network. Most desktop-to-desktop sync goes here.
2. **Direct via STUN** — public-IP-to-public-IP across NAT. STUN servers only return your IP, they don't relay traffic.
3. **TURN relay** — fallback when direct paths fail (e.g. iOS Safari ↔ Chromium peers, where Chromium uses mDNS host candidates that WebKit cannot resolve reliably).

The current `rtcConfig` uses Google's public STUN and the [Open Relay](https://www.metered.ca/tools/openrelay/) public TURN as fallback.


## Roadmap

- [ ] Settings page — Import/Export, full data reset
- [ ] PWA icons & splash screen
- [ ] Onboarding
