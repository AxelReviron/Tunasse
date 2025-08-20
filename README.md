# 💵 Tunasse

**Tunasse** is an open source financial management app built with Laravel and Vue 3 (via Inertia).  
It allows you to organize your transactions into budgets, manage accounts (checking, savings, credit, investment)

## ✨ Features

- Create and manage **accounts**
- Create and manage **transactions**
- Organize **budgets**


## 🗒️ TODO
### Back
- [x] Migrations
- [x] Factories + Seeders
- [x] Models
- [x] Controllers
- [ ] API Resources
- [ ] Form Requests
- [ ] Role and permissions (policies)
- [ ] Routes
- [ ] Observers / Events
- [ ] Auth
- [ ] Filament
- [ ] Tests
### Front
- [ ] State management
- [ ] Layout
- [ ] Component
- [ ] Loading / Error states
#### Page
- [ ] Login page
- [ ] Register page
- [ ] Home page
- [ ] Transactions page
  - [ ] Income page
  - [ ] Expense page
- [ ] Accounts page
  - [ ] Specific account page
- [ ] Budget page
  - [ ] Specific budget page
- [ ] Locales (FR, EN)


## 💡 Ideas
- Connector/Scrapper for bank account
- Mobile app

## 💻 Installation

#### 1. Clone the repository
```bash
git clone https://github.com/AxelReviron/Tunasse.git
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
