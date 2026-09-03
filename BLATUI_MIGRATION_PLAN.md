# BlatUI Migration — Effort Assessment & Phase Plan

Full UI migration from the current hand-rolled Tailwind styling to [BlatUI](https://github.com/anousss007/blatui)
(shadcn/ui for Blade + Laravel + Alpine + Tailwind), **no legacy-style fallback** — every view ends up on
BlatUI components.

## Current state (baseline, measured 2026-09-03)

| Area | Count | Notes |
|---|---|---|
| Total `.blade.php` views | 123 | |
| `src/Themes/views/` | 19 | Core CRUD engine — `browse`, `form`, `detail`, `table-list`, `table-grid`, `sidebar`, `pagination`, modals. Used by **every** generated CRUD module (Menu, Role, User, and all future `cb:install` modules), highest leverage & highest blast radius. |
| `src/Components/` | 53 | Shared Blade components, reused across modules. |
| `src/Modules/*/views/` | 51 | Per-feature views: ApiBuilder (3), AuditLog (1), Auth (5), Dashboard (1), ModuleBuilder (14), PageBuilder (20), Profile (1), QueryBuilder (2), Setting (4). Menu/Role/User have 0 own views — they run entirely on the Themes engine. |
| Livewire components | 51 | 51 files with `render()` (37 `extends Component`, rest abstract/trait-based). Many drive interactive UI (modals, dropdowns, live search, table state) that must keep working after the markup swap. |
| Build pipeline | gulp + PostCSS, Tailwind **v3.4.14**, `tailwindcss-rtl` plugin | Compiles to a **static pre-built bundle** shipped in the package (`src/Themes/assets/css/app.min.css`, 65.6K). Classes not present at last build silently no-op — already a known footgun. |
| Consumers | Composer package installed into arbitrary host Laravel apps (11/12/13, PHP 8.2+) | Host app never runs our build step — it only gets the compiled CSS. This is why a copy-owned-code library (BlatUI) fits and a runtime-dependency library (Flux) doesn't. |

## Hard prerequisite

BlatUI requires **Tailwind v4** (`@theme inline`, oklch colors). This repo is pinned to v3.4.14, so a
Tailwind v3→v4 upgrade is not optional — it's Phase 0. Things to handle early:

1. **`tailwindcss-rtl` — drop it, don't port it.** (Verified 2026-09-03: zero `rtl:` variant usage
   across all blade files in `src/`; the only RTL support is the `dir="rtl"` attribute in
   `layout-app`/`layout-empty`, driven by locale. The plugin produces no CSS output for this codebase.)
   Remove it from `package.json` + `tailwind.config.js`. If real RTL styling is wanted later, Tailwind v4
   handles `dir`-based logical properties natively.
2. **gulp + `gulp-postcss` + `cssnano` pipeline** — Tailwind v4's recommended build is the `@tailwindcss/postcss` or `@tailwindcss/cli` plugin, not the v3 `tailwindcss()` PostCSS plugin. The gulpfile needs rework, not just a version bump.
3. **BlatUI JS engine must be pre-bundled and shipped.** BlatUI components need Alpine plugins
   (`@alpinejs/anchor`, `@alpinejs/collapse`, `@alpinejs/focus`, `@floating-ui/dom`) plus the
   `registerBlatUI()` engine. This package's consumers never run a JS build, and Alpine currently comes
   from Livewire 3's own bundle (no npm Alpine anywhere in this repo). So: bundle the engine + plugins
   once here (esbuild/rollup) into a static file under `src/Themes/assets/js/`, register it via
   `CbThemeAssetRegistrar::addJs()`, and boot via the `alpine:init` hook (`registerBlatUI(window.Alpine)`)
   so it attaches to Livewire's Alpine instance — do NOT import/but a second Alpine.
4. **Composer peers.** BlatUI requires `gehrisandro/tailwind-merge-laravel` +
   `mallardduck/blade-lucide-icons`. These become new requirements of this package — impacts all existing
   consumers, belongs in the breaking-change release note. Icon system must also migrate from the custom
   `Icon::BAR`-style inline SVG constants to Lucide (`mallardduck/blade-lucide-icons`).
5. **`cb-custom.css` consumer flow breaks under v4.** `cb:custom-css`
   (`src/Commands/GenerateCustomCssCommand.php`) teaches consumers v3 syntax (`@tailwind utilities`,
   `npx tailwindcss -c ...`). v4 syntax differs (`@import "tailwindcss"`; standalone CLI binary). Update
   the template + tips in the same release, and call it out in the release notes.

## Overall effort estimate

**~6–8 weeks of focused solo work** (with AI-assisted implementation), assuming no scope surprises. Breakdown below. This assumes the person doing it also does the manual QA pass per module (can't be skipped — Tailwind's static-bundle footgun means visual bugs are silent).

---

## Phase 0 — Foundation & tooling (3–5 days) — ✅ DONE 2026-09-03

**Status: complete**, on branch `feat/blatui-phase0`. What actually shipped:

- Tailwind 3.4.14 → v4.3.3 (`tailwind.config.js` deleted; config now lives in
  `src/Themes/assets/css/app.css`: `@import "tailwindcss"`, `@source` globs, `@custom-variant dark`,
  `@source inline` for the old safelist). `tailwindcss-rtl` removed.
- BlatUI design tokens (oklch palette, radius, dark/light presets) appended to `app.css` from the
  published `blatui.css` foundation (sandbox: `cb-test`, `php artisan vendor:publish
  --tag=blatui-foundations`).
- `gulpfile.js` rewritten: `@tailwindcss/cli` compiles the entry, three `@apply` fragment files
  (`AlertMessage/alert.css`, PageBuilder `heading.css`/`image.css`) are compiled separately via
  `@tailwindcss/postcss` + `@reference`, then concat + cssnano → `app.min.css` (~150K).
- BlatUI JS engine (`blatui-core.js` + Alpine plugins + `@floating-ui/dom`) pre-bundled with esbuild
  (`npm run build:js`) to `src/Themes/assets/js/blatui.min.js` (92K, IIFE), registered via
  `CbThemeAssetRegistrar::addJs()` in `CbThemeServiceProvider`. Source kept in
  `src/Themes/assets/js/blatui-src/`. It only listens on `alpine:init` — bundling/starting its own
  Alpine caused "multiple instances of Alpine" and broke Livewire (found during testing).
- `composer.json`: `mallardduck/blade-lucide-icons ^2.0` added to require (works on L11+);
  `gehrisandro/tailwind-merge-laravel ^1.2|^1.4` added to require-dev (its v1.4 needs L12+ — pin
  `^1.2` for L11 consumers if it ever moves out of require-dev).
- `layout-empty.blade.php` now also emits `@cbAssets` (auth pages need the engine too).
- Verified in `cb-test` (fresh `cb:install` via expect + Playwright): login 200, dashboard/user
  browse/user form/module-builder render correctly, Livewire live-search works, dark-mode toggle
  flips `.dark` (oklch colors), zero JS console errors. Pre-existing (not Phase 0): `/cms/setting/appearance`
  500s — `AppearanceSetting` full-page Livewire component lacks `->layout(...)`, broken on `main` too.

Original task list (for reference):

- Upgrade `tailwindcss` 3.4.14 → v4, run `@tailwindcss/upgrade` codemod.
- Remove `tailwindcss-rtl` (see hard prerequisite #1 — no usage to port).
- Rewrite `gulpfile.js` build step for Tailwind v4's PostCSS plugin; confirm output still lands in both `src/Themes/assets/css/` and `public/vendor/crudbooster/themes/assets/css/`.
- Add BlatUI composer peers (`gehrisandro/tailwind-merge-laravel`, `mallardduck/blade-lucide-icons`) to `composer.json`.
- Build the BlatUI JS engine bundle (Alpine plugins + `registerBlatUI`) into `src/Themes/assets/js/` and register via `CbThemeAssetRegistrar::addJs()`; verify it boots against Livewire's Alpine (`alpine:init` hook).
- Run `blatui:init` **inside a sandbox Laravel app** (e.g. `cb-test`) — the CLI targets a host app's
  `resources/` dirs, not a composer package. Establish design tokens (`@theme inline`, oklch palette,
  radius, dark mode via `class` strategy — must match existing `darkMode: 'class'` setup), then copy the
  resulting `blatui.css` foundations into this repo's build inputs.
- Verify `content` globs in the Tailwind config still match BlatUI's expected file locations for content scanning.
- Rebuild `app.min.css`, confirm no regression on an untouched view (sanity check the pipeline itself before touching any markup).

**Exit criteria:** clean build, a single test view still renders correctly, BlatUI JS engine boots (test one Alpine plugin, e.g. collapse, on the test view). *(All met — see status note above.)*

## Phase 1 — Core component library (1–1.5 weeks)

**Goal:** Pull in and adapt the BlatUI components this project actually needs; establish the pattern for wiring Alpine-driven components (dialog, dropdown, popover, combobox, sheet) to Livewire's `wire:model`/`wire:click`/event dispatch model.

- Inventory current UI primitives in `src/Components/` (53 files) and `src/Themes/views/components/` and map each to its BlatUI equivalent: button, input, select, checkbox, radio, switch, dialog/modal, dropdown-menu, table, pagination, tabs, alert, badge, avatar, card, toast, tooltip, sheet/drawer, command palette, date picker.
- Run `blatui:add <component>` in the sandbox app, then **move the copied files into this package**
  (`src/` — suggest `src/Themes/components/ui/` or similar) and adapt namespace/view-path registration.
  The CLI never targets this repo directly.
- For each, copy via BlatUI CLI, then adapt: Livewire-specific bindings (`wire:model.live`, `wire:loading`, `wire:target`), CB-specific props (this project passes config arrays into components rather than plain slots in a few places — check `src/Components/*.php` class-based components).
- Establish one **reference implementation** per interactive pattern (e.g. one working modal wired to Livewire, one working dropdown, one working async-search combobox) — everything downstream reuses these, don't reinvent per-module.
- Decide dark mode strategy carry-over (already `class`-based, should map cleanly).

**Exit criteria:** a small internal style-guide/demo page exercising every migrated primitive in isolation, all interactive states (hover/focus/active/disabled/loading) verified in-browser.

## Phase 2 — Core CRUD engine: `src/Themes/views/` (1–1.5 weeks)

**Goal:** Reskin the 19 files that back every CRUD module (including Menu, Role, User which have no views of their own).

Highest blast radius phase — a mistake here breaks every generated module, not just one screen.

- `layout-app.blade.php`, `layout-empty.blade.php`, `sidebar.blade.php` — shell/nav.
- `browse.blade.php`, `table-list.blade.php`, `table-grid.blade.php`, `pagination.blade.php` — list views, bulk actions, filters.
- `form.blade.php`, `form-input.blade.php`, `detail.blade.php` — CRUD form/detail rendering (this is generic/dynamic — renders arbitrary field configs, so needs careful testing across different field types: text, select, relation, file upload, etc.).
- `components/` subfolder (header, modal-bulk-confirmation, modal-import, imagepreview, form-title, form-content, detail-content) — 7 files.
- `export/pdf.blade.php` — check whether PDF export styling depends on the compiled CSS bundle at all (likely a separate concern, low BlatUI-relevance).

**Exit criteria:** spin up `cb-test`, generate a throwaway module via ModuleBuilder, walk create/edit/browse/detail/delete/import/export end-to-end.

## Phase 3 — Module-by-module reskin (2–2.5 weeks)

**Goal:** Migrate the 51 module-specific views. Ordered by complexity, not module name:

| Sub-phase | Modules | Views | Why this grouping |
|---|---|---|---|
| 3a — simple | AuditLog (1), Dashboard (1), Profile (1), Setting (4) | 7 | Mostly list/detail/form patterns already solved in Phase 2, low interaction complexity. |
| 3b — auth | Auth (5) | 5 | Public-facing, no sidebar/shell — separate layout path, must look right unauthenticated too. |
| 3c — builders, low interactivity | ApiBuilder (3), QueryBuilder (2) | 5 | Config-heavy forms, moderate custom JS (API test modal, query preview). |
| 3d — builders, high interactivity | ModuleBuilder (14) | 14 | Schema builder UI — drag/drop-adjacent interactions, dynamic field arrays, this is where most custom Alpine logic lives (`ModuleFormTableSchema.php` Livewire component). |
| 3e — PageBuilder (highest complexity) | PageBuilder (20) | 20 | Visual page/element builder with per-element config panels — 7 element types (BoxCounter, Chart, GoogleMap, Heading, Image, Paragraph, Table), each with its own `ElementX.php` + `config.blade.php`/`view.blade.php`. Likely needs new BlatUI-based patterns not covered by Phase 1's reference set (canvas/drag areas, live preview panes). Budget extra buffer here specifically. |

**Exit criteria per sub-phase:** every view in that group manually walked in `cb-test`, no visual regression vs. a documented "before" screenshot, all Livewire interactions (validation errors, loading states, real-time updates) confirmed working.

## Phase 4 — Cleanup, QA pass, release (3–5 days)

- Remove all legacy Tailwind classes/utility patterns not part of the BlatUI design system — "no fallback" means no dead CSS or half-migrated views left in the bundle.
- Remove dead assets surfaced during migration: `src/Themes/assets/js/flowbite.min.js` is shipped but referenced nowhere in `src/` (verified 2026-09-03). Audit other unused assets before final build.
- Check Summernote's CDN stack (jQuery 3.5 + Bootstrap 3.4, pulled in `SummernoteServiceProvider`) for visual clashes against BlatUI styles.
- Full regression pass across all modules in `cb-test` (per the repo's existing manual testing flow — no CI here).
- Rebuild final `app.min.css`, verify bundle size, check `storage/logs/laravel.log` for view errors across every screen.
- Update the `cb:custom-css` command template for Tailwind v4 syntax (see hard prerequisite #5).
- Update `CODEBASE_DOCUMENTATION.md` / `doc_cb.md` if component usage patterns changed for downstream consumers who extend/override views.
- Bump `composer.json` version (**major** — breaking: new composer peers, Tailwind v4, view overhaul), tag release per the repo's existing release flow (`CLAUDE.md` → Release flow section).

**Exit criteria:** tagged release, smoke-tested against a fresh `cb:install` (not just the existing `cb-test` app, to catch any first-run/scaffolding assumptions).

---

## Risk register

| Risk | Why it matters | Mitigation |
|---|---|---|
| BlatUI JS engine vs Livewire's Alpine | Double-booting Alpine breaks everything; consumers have no JS build step | Pre-bundle engine once (Phase 0), boot via `alpine:init` hook onto `window.Alpine`; never `npm install alpinejs` separately. Validate early with one interactive component. |
| New composer peers force upgrades on consumers | `tailwind-merge-laravel` + `blade-lucide-icons` are new hard requirements of this package | Check their Laravel 11/12/13 + PHP 8.2 compatibility before Phase 1; document in breaking-change release notes. |
| Static CSS bundle footgun | A class BlatUI's copied component uses but that isn't in the last local build silently no-ops — same failure mode already documented in this repo's `CLAUDE.md` | Rebuild (`gulp css`) after every batch of component work, never trust unbuilt markup |
| PageBuilder element config panels | Most bespoke JS/Livewire wiring in the codebase (BoxCounter, Chart, GoogleMap, Heading, Image, Paragraph, Table elements each have `config.blade.php` + `view.blade.php` — 7 elements, not 3 as first assumed) | Treat as its own mini-project inside Phase 3e, prototype one element fully before doing the rest |
| ModuleBuilder schema editor | Dynamic repeating field arrays (`ModuleFormTableSchema.php`) — easy to break Livewire's data-binding when restructuring markup | Reference implementation from Phase 1 (repeatable list pattern) should be validated against this specific component before Phase 3d starts |
| Summernote/TinyMCE/Trix rich-text stacks | Summernote pulls jQuery + Bootstrap 3 from CDN — potential style clashes with BlatUI's preflight | Smoke-test each editor type in Phase 3; scope clash fixes to the editor container |
| Downstream consumers overriding views | This is a package — some `cb:install` users may have published/customized views from the old design system | Out of scope to fix for them, but call out clearly in release notes as a breaking change (major version bump) |

## Suggested sequencing note

Phases 0–2 are strictly sequential (each depends on the last). Phase 3's sub-phases can be parallelized across sessions/agents once Phase 1's reference patterns exist, since modules don't depend on each other — 3a/3b/3c could run concurrently with 3d/3e if more than one person/session is available. Phase 4 is sequential and must come last.
