# Changelog

## 2026-05 — Livewire 3 incremental refactor

Migrated the interactive surface area of the application from inline-JS
controllers to Livewire 3 components, while preserving every existing
feature and template look. Released as a series of focused pull requests.

### Highlights

- **Livewire 3.8** wired into the Blade layout via `@livewireStyles` /
  `@livewireScripts`.
- **Pages converted to Livewire components**:
  - `App\Livewire\Verb\Search` (home + `/search`) and
    `App\Livewire\Verb\RecentHistory`.
  - `App\Livewire\History\Index` (`/history`).
  - `App\Livewire\Admin\Dashboard` (`/admin/dashboard`) with `wire:click`
    Refresh / Clear Cache / Optimize.
- **Service layer** introduced under `App\Services`:
  - `DeepSeekTranslator` (translate, batch, check + caching + local
    dictionary fallback).
  - `VerbSearchService` (Qutrub API wrapper).
  - `SearchHistoryService` (CRUD + schema availability check).
  - `AdminStatsService` (dashboard stats, system health).
- **Form Requests**: `TranslateRequest`, `BatchTranslateRequest`,
  `StoreSearchHistoryRequest`, `SearchVerbRequest`, `ChatRequest`.
- **Security/config**: hardcoded DeepSeek API keys removed from
  `TranslationController` and `ChatController`; replaced by
  `config('services.deepseek.*')` reading from `.env`. `.env.example`
  updated.
- **Routes**: legacy duplicate `/translation/*` prefix dropped in favour
  of `/api/translate*`. `home.blade.php` and `history.blade.php` shrunk to
  thin Livewire wrappers (727 → 5 LOC for `home.blade.php`).
- **Assets**: `TranslationEnhanced` extracted from inline `<script>` into
  a proper ES module at `resources/js/translation-enhanced.js`. Legacy
  files `search.js`, `translation.js`, `translation-debug.js`,
  `translation-extend.js`, and `search-history.js` deleted.

### Pull request trail

| PR  | Branch                               | Summary                                                   |
| --- | ------------------------------------ | --------------------------------------------------------- |
| #1  | `chore/pre-refactor-baseline`        | Baseline WIP UI tweaks before refactor.                   |
| #3  | `chore/bootstrap-workflow`           | GitHub labels + `docs/REFACTOR_PLAN.md`.                  |
| #5  | `chore/install-livewire`             | Install Livewire 3.5+, smoke-test component.              |
| #7  | `chore/deepseek-env-config`          | API keys to `.env` + `config/services.php`.               |
| #9  | `refactor/service-layer`             | Service classes + slim controllers.                       |
| #11 | `refactor/form-requests`             | Form Request classes for validation.                      |
| #13 | `feature/livewire-history`           | Livewire `History\Index`.                                 |
| #15 | `feature/livewire-home-search`       | Livewire `Verb\Search` + `RecentHistory`.                 |
| #17 | `feature/livewire-admin-dashboard`   | Livewire `Admin\Dashboard`.                               |
| #19 | `chore/final-cleanup`                | Dead-code removal, README + this changelog, E2E sweep.    |

### Breaking changes

None at the user-visible layer. Internal:

- `App\Http\Controllers\VerbController` removed (route `/search-verb` and
  the `result.blade.php` view it referenced were unused).
- `App\Http\Controllers\SearchHistoryController` removed; routes for
  history `POST` / `DELETE` removed (Livewire owns these now).
- Smoke-test endpoint `/dev/livewire-check` and component
  `App\Livewire\Hello` removed after the migration completed.

### Known follow-ups

- `AdminStatsService::databaseSize` still queries MySQL
  `information_schema`; on the current SQLite default it returns `'N/A'`.
  Tracked as a separate roadmap item.
- `App\Services\GoogleTranslateService` is unused after the DeepSeek
  refactor and is a candidate for removal in a future cleanup.
- Login / Register pages remain controller + Blade by design.
