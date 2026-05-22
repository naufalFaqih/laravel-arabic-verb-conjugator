# Refactor Plan — Laravel 11 → Livewire 3 (Incremental)

Dokumen ini merangkum rencana refactor yang dijalankan terhadap proyek
**Tashrif Arabic Verbs / ArabicMorph**. Tujuannya agar codebase lebih modular
dan mudah dimaintain, tanpa mengubah fungsionalitas, tampilan, atau skema
database.

## Prinsip

- **Incremental** — Hanya halaman interaktif yang dipindah ke Livewire 3.
  `landing`, `auth/login`, `auth/register`, `chat`, `admin/users`,
  `admin/user-detail`, `admin/monitoring` tetap pakai controller + Blade.
- **No behavior change** — Tampilan, URL, dan response API harus sama persis.
- **No DB schema change** — Tidak ada migration baru.
- **Cleanup ringan** — Service classes, Form Requests, API key ke `.env`,
  hapus rute terjemahan duplikat.
- **Workflow rapi** — 1 task = 1 issue + 1 branch + 1 PR via `gh` CLI.

## Tasks

| # | Branch | Issue | Output |
|---|--------|-------|--------|
| 1 | `chore/bootstrap-workflow` | Bootstrap repo & workflow | Label GitHub, dokumen ini |
| 2 | `chore/install-livewire` | Install Livewire 3 & skeleton | `composer require livewire/livewire`, komponen `Hello` |
| 3 | `chore/deepseek-env-config` | Pindahkan API key ke `.env` & `config/services` | API key tidak hardcoded, rute terjemahan dikonsolidasikan |
| 4 | `refactor/service-layer` | Ekstrak logika ke Service classes | `DeepSeekTranslator`, `VerbSearchService`, `SearchHistoryService`, `AdminStatsService` |
| 5 | `refactor/form-requests` | Form Request classes | `TranslateRequest`, `BatchTranslateRequest`, `StoreSearchHistoryRequest`, `SearchVerbRequest` |
| 6 | `feature/livewire-history` | Livewire `History\Index` | Halaman `/history` reactive |
| 7 | `feature/livewire-home-search` | Livewire `Verb\Search` (+ `Verb\RecentHistory`) | Halaman `/` & `/search` reactive |
| 8 | `feature/livewire-admin-dashboard` | Livewire `Admin\Dashboard` | Dashboard reactive dengan refresh/clear-cache button |
| 9 | `chore/final-cleanup` | Cleanup & dokumentasi | Hapus dead code, update README, smoke test E2E |

## Branching & PR Workflow

1. Branch dari `main`: `git checkout main && git pull && git checkout -b <branch>`.
2. Kerjakan task, commit dengan pesan deskriptif.
3. Push & buat PR: `gh pr create --base main --head <branch> --title "..." --body "Closes #<issue>"`.
4. Self-review checklist manual smoke test pada PR description.
5. Squash merge: `gh pr merge <PR> --squash --delete-branch`.
6. Update lokal: `git checkout main && git pull`.

## Smoke Test Checklist (per PR)

Setiap PR menyertakan checklist manual yang relevan:

- Halaman terkait masih render & fungsional.
- Auth flow (login/logout) tetap jalan.
- Search verb + translate tetap menghasilkan output yang sama.
- History tersimpan & bisa dihapus.
- Admin dashboard menampilkan stats yang akurat.

## Environment

- PHP `^8.2`, Laravel `^11.31`, Livewire `^3` (akan ditambah di Task 2).
- Tailwind 3, Alpine.js 3 (CDN), Vite 6.
- Database: SQLite (default project).

## Out of Scope

- Penambahan automated test (manual only, sesuai keputusan project owner).
- Refactor halaman admin selain dashboard.
- Refactor `ChatController` ke Livewire (tetap controller).
- Perbaikan bug `AdminController::getDatabaseSize()` yang query MySQL
  `information_schema` (project pakai SQLite). Akan didokumentasikan di Task 4
  tapi tidak diperbaiki pada refactor ini.
