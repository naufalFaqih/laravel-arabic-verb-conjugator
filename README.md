# Tashrif Arab — Arabic Verb Conjugation App with AI Translation

<p align="center">
  <img src="public/img/logo am.png" width="200" alt="ArabicMorph Logo">
</p>

A Laravel 11 + **Livewire 3** application that helps learners explore Arabic
verb conjugation (Tashrif Lughowi) and provides AI-powered Arabic →
Indonesian translation via the DeepSeek API.

## ✨ Features

### Verb conjugation
- Search Arabic verbs and view a complete tashrif table (8 conjugation
  categories × 14 pronouns).
- Madhi / Mudhori / Amar summary block.
- Verb metadata (transitive vs. intransitive, trilateral, etc.).
- Suggested chapters / related forms.
- Powered by the public [Qutrub API](http://qutrub.arabeyes.org/api).

### AI translation
- DeepSeek-driven Arabic → Indonesian translation with prompt tuning for
  classical Arabic verbs.
- Permissive validation, automatic retry, and a curated local dictionary as
  fallback when the API is unreachable.
- Two-layer caching: Laravel `Cache` on the server + browser `localStorage`.

### Auth & history
- Email/password registration + login (Laravel built-in auth).
- `last_login_at` tracking.
- Personal **search history** with delete-one / delete-all actions.

### Admin panel
- Dashboard with user/search statistics, recent activity, and system health.
- User management page (list, detail, toggle admin).
- Monitoring page using Telescope-backed metrics.
- Inline buttons to clear application cache and run optimisation.

## 🏗️ Architecture (after Livewire refactor)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php          (users / monitoring / userDetail)
│   │   ├── ApiController.php            (thin wrapper → VerbSearchService)
│   │   ├── Auth/                        (Login, Register)
│   │   ├── ChatController.php           (DeepSeek chat proxy)
│   │   └── TranslationController.php    (thin wrapper → DeepSeekTranslator)
│   └── Requests/                        (Form Requests for validation)
├── Livewire/
│   ├── Admin/Dashboard.php              ← /admin/dashboard
│   ├── History/Index.php                ← /history
│   └── Verb/
│       ├── RecentHistory.php            ← partial on home page
│       └── Search.php                   ← /search & home page
├── Models/
└── Services/
    ├── AdminStatsService.php
    ├── DeepSeekTranslator.php
    ├── SearchHistoryService.php
    └── VerbSearchService.php

resources/
├── js/
│   ├── app.js                           (entry: bootstrap + translation-enhanced)
│   ├── arabic-keyboard.js               (Arabic on-screen keyboard)
│   ├── bootstrap.js                     (Axios + CSRF)
│   └── translation-enhanced.js          (client-side translate cache)
└── views/
    ├── components/{layout,navbar,header,nav-link}.blade.php
    ├── livewire/
    │   ├── admin/dashboard.blade.php
    │   ├── history/index.blade.php
    │   └── verb/{search,recent-history}.blade.php
    ├── admin/{dashboard,users,user-detail,monitoring}.blade.php
    └── auth/{login,register}.blade.php
```

## 🚀 Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- MySQL or SQLite database

### Setup

```bash
# 1. Clone & install dependencies
git clone <repository_url>
cd latihanLaravel11
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate
# edit .env and set: DB_*, DEEPSEEK_API_KEY, etc.

# 3. Database
php artisan migrate
php artisan db:seed   # optional — creates an admin user

# 4. Build assets & serve
npm run build         # or: npm run dev
php artisan serve
```

The application is available at `http://127.0.0.1:8000`.

### Required environment variables

| Variable                | Description                                       |
| ----------------------- | ------------------------------------------------- |
| `DEEPSEEK_API_KEY`      | DeepSeek API token (required for translation/chat) |
| `DEEPSEEK_API_URL`      | DeepSeek endpoint, default `https://api.deepseek.com/v1/chat/completions` |
| `DEEPSEEK_MODEL`        | Model name, default `deepseek-chat`                |
| `GOOGLE_TRANSLATE_KEY`  | Optional, used by the legacy Google Translate service |

## 📖 Usage

1. Open the homepage and enter an Arabic verb (e.g. `كَتَبَ`).
2. Click **Tashrif** — the Livewire component fetches conjugation data,
   renders the 8-column tashrif table, and dispatches an event so the
   client-side translator can fill in Indonesian translations.
3. Logged-in users automatically have searches saved to the history page
   (`/history`), with delete-one/delete-all powered by Livewire actions.
4. Admins can visit `/admin/dashboard` for statistics and quick actions.

## 🔧 API endpoints (canonical)

| Method | URI                       | Description                                |
| ------ | ------------------------- | ------------------------------------------ |
| GET    | `/api/search-verb`        | Qutrub conjugation lookup (`?verb=`).      |
| POST   | `/api/translate`          | Translate single Arabic → Indonesian text. |
| POST   | `/api/translate/check`    | Probe DeepSeek connectivity.               |
| POST   | `/api/translate/batch`    | Batch-translate up to N strings.           |
| POST   | `/chat`                   | DeepSeek chat proxy (CSRF exempt).         |

## 🧪 Manual smoke-test checklist

After every change:

1. `GET /` → 200, landing page renders.
2. `GET /search` → 200, Livewire form rendered.
3. `GET /search?query=كَتَبَ` → 200, full results auto-load.
4. Login as a regular user → `GET /history` shows entries; delete-one and
   delete-all work without page reload.
5. Login as an admin → `GET /admin/dashboard` shows stats; Refresh / Clear
   Cache / Optimize buttons trigger their respective Livewire actions.
6. `GET /admin/users`, `/admin/monitoring`, `/admin/users/{id}`,
   `/telescope` still load correctly.
7. Sanity-check translation by searching a verb while authenticated and
   confirming Arabic strings receive Indonesian glosses.

## 🤝 Contributing

```bash
composer install --dev
npm install
./vendor/bin/pint        # PHP formatter
php artisan test         # currently scaffolds only — see Roadmap
npm run dev              # asset watcher
```

### Branching & PR workflow

| Prefix       | Use for                                |
| ------------ | -------------------------------------- |
| `feature/`   | New features (Livewire components, …). |
| `refactor/`  | Behaviour-preserving restructuring.    |
| `chore/`     | Build, deps, docs, infra.              |

Open one PR per task. Each PR includes a manual smoke-test checklist.

## 🗺️ Roadmap

- Convert `admin/users`, `admin/user-detail`, `admin/monitoring` to Livewire.
- Replace `AdminStatsService::databaseSize()` MySQL `information_schema`
  query with a SQLite-aware implementation (current project default is
  SQLite — the legacy method still works only when MySQL is configured).
- Add Pest/PHPUnit tests for Livewire components and Services.
- Convert chat into a Livewire component with streaming responses.

## 📄 License

MIT — see [LICENSE](LICENSE).

## 🙏 Acknowledgements

- Laravel Framework
- Livewire 3
- Tailwind CSS
- Qutrub project
- DeepSeek AI
