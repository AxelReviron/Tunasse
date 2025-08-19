# 💵 Tunasse

**Tunasse** is an open source financial management app built with Laravel and Vue 3 (via Inertia).  
It allows you to organize your transactions into budgets, manage accounts (checking, savings, credit, investment)

🌴 Trip planning app with interactive maps, personalized notes, and packing lists. Organize solo trips or group adventures with ease.
## ✨ Features

- Create and manage **accounts**
- Create and manage **transactions**
- Organize **budgets**


## 🗒️ TODO
- [x] Ability to switch languages (currently English-only)
- [x] Handle 429 (Rate Limiting)
- [x] Settings Page (change user info, add OpenRouteService API key)
- [ ] Refactor API calls into dedicated services
- [ ] Use composables for UI or component-related logic
- [ ] Centralize store management (avoid direct state changes in pages/components)
- [ ] Privacy Policy Page
- [ ] Terms Of Service Page
- [ ] About Page
- [ ] Packing list management (items + quantity)
- [ ] Refactor locale file structure
- [ ] Use MySQL container for CI and enable code coverage


## 💻 Installation

#### 1. Clone the repository
```bash
git clone https://github.com/AxelReviron/tunasse.git
cd tunasse
```

#### 2. Copy the environment file
```bash
cp .env.example .env
```

#### 3. Update .env variables (for the db)
see `compose.dev.yaml` for required vars.

#### 4. Start Docker containers
```bash
docker-compose -f compose.dev.yaml up -d
```

#### 5. Run migrations
```bash
docker exec app php artisan migrate
```
#### 6. (Optional) Seed sample data
```bash
docker exec app php artisan db:seed
```
