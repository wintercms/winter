# Agent guide — Winter CMS core

Winter core sits on top of [Winter Storm](https://github.com/wintercms/storm) (installed at `vendor/winter/storm/`), which sits on top of Laravel, which sits on top of Symfony components. The helper you want almost certainly already exists at one of those layers. **Search Storm → Laravel → Symfony before writing any "small utility"**, often with safer edge-case handling than a fresh implementation would have.

## Where to look (in this order)

1. **Storm itself** — each `vendor/winter/storm/src/<Module>/README.md` catalogues that module's public API:
   - `Filesystem/` — `PathResolver` (`resolve`, `within`, `join`, `standardize`), `Filesystem` (extends Illuminate's; adds `isAbsolutePath`, `symbolizePath`, `existsInsensitive`, `chmodRecursive`)
   - `Support/` — strings, arrays, class loading
   - `Network/`, `Html/`, `Parse/`, `Database/`, `Halcyon/`, `Auth/`, etc.
   - Path helpers (always loaded): `themes_path()`, `plugins_path()`, `media_path()`, `uploads_path()`, `temp_path()` — use these instead of `base_path('themes')` etc.

2. **Laravel (Illuminate)** — everything Laravel ships is available:
   - `Illuminate\Support\Str` — `Str::startsWith/endsWith/contains/before/after/between/slug/camel/snake/kebab/studly/random/uuid/limit/mask/finish/start/of/headline/title`. Use instead of regex one-liners.
   - `Illuminate\Support\Arr` — `Arr::get/set/has/forget/only/except/dot/undot/flatten/pluck/wrap/first/last/where`. Use instead of nested foreach.
   - `Illuminate\Support\Collection` (via `collect()`) — chainable map/filter/reduce.
   - `Illuminate\Filesystem\Filesystem` — `deleteDirectory()`, `cleanDirectory()`, `copyDirectory()`, `moveDirectory()`, `allFiles()`, `glob()`, `isDirectory()`, `prepend()`, `append()`, `replace()`, `hash()`.
   - Global helpers: `data_get/set/fill`, `value`, `tap`, `optional`, `transform`, `head`, `last`, `class_basename`, `now`, `today`, `e`, `__`/`trans`, `cache`, `config`, `env`, `app`, `resolve`, `route`, `url`, `report`, `rescue`, `retry`, `throw_if`/`unless`, `abort`/`abort_if`/`unless`.
   - Facades: `Cache`, `Config`, `DB`, `Event`, `File`, `Hash`, `Http`, `Lang`, `Log`, `Mail`, `Queue`, `Redis`, `Route`, `Schema`, `Session`, `Storage`, `URL`, `Validator`, `View`.

3. **Symfony components** at `vendor/symfony/`: `console`, `css-selector`, `error-handler`, `event-dispatcher`, `finder`, `http-foundation`, `http-kernel`, `mailer`, `mime`, `process`, `routing`, `string`, `translation`, `uid`, `var-dumper`, `yaml`. Most commonly reached for:
   - `Symfony\Component\Finder\Finder` — `Finder::create()->files()->name('*.less')->in($dir)` replaces RecursiveIteratorIterator chains.
   - `Symfony\Component\Filesystem\Filesystem` — `dumpFile()` (atomic write), `mirror()`, `mkdir()` (idempotent), `remove()`, `symlink()`.
   - `Symfony\Component\Process\Process` — safe external command execution instead of `exec()`/`shell_exec()`.
   - `Symfony\Component\Yaml\Yaml` — strict YAML.
   - `Symfony\Component\String\` — Unicode-aware strings.
   - `Symfony\Component\Uid\Uuid`/`Ulid` — UUID/ULID generation.

`grep -rl 'function <thing>' vendor/winter/storm/src/ vendor/laravel/framework/src/ vendor/symfony/` is a 10-second check.

## Concrete substitutions worth memorising

Paths and filesystem:

| If you reach for… | Use this instead |
|---|---|
| `realpath()` + null-check + slash-trim | `\Winter\Storm\Filesystem\PathResolver::resolve()` |
| `str_starts_with($path, $root)` to gate file access | `PathResolver::within($path, $root)` — separator-boundary safe |
| Manual `base_path('themes')` / `base_path('plugins')` | `themes_path()` / `plugins_path()` (Storm's autoloaded helpers) |
| Recursive `rmrf` in tests | `\File::deleteDirectory($path)` (Laravel facade) |
| Detect absolute path | `(new \Winter\Storm\Filesystem\Filesystem())->isAbsolutePath($path)` |
| Custom path-symbol resolution (`~/...`) | `(new \Winter\Storm\Filesystem\Filesystem())->symbolizePath($path)` |
| `str_replace('\\', '/', $path)` (cross-platform comparison) | `(new \Winter\Storm\Filesystem\Filesystem())->normalizePath($path)` |
| `str_replace('/', DIRECTORY_SEPARATOR, $path)` (handing to OS API) | `\Winter\Storm\Filesystem\PathResolver::standardize($path)` |
| Atomic file write (avoid partial-write races) | `(new \Symfony\Component\Filesystem\Filesystem())->dumpFile($path, $contents)` |
| Find files matching a pattern | `\Symfony\Component\Finder\Finder::create()->files()->name('*.ext')->in($dir)` |

Strings and arrays:

| If you reach for… | Use this instead |
|---|---|
| `preg_match('/^prefix/', $s)` | `Str::startsWith($s, 'prefix')` (accepts array of prefixes) |
| Manual `strpos !== false` | `Str::contains($s, $needle)` |
| `strtolower`-then-replace slug generation | `Str::slug($s)` |
| Random hex/string for tmp paths, tokens | `Str::random()` / `Str::uuid()` |
| Deep array key access with null safety | `data_get($array, 'a.b.c', $default)` |
| Pulling subset of array keys | `Arr::only($array, [...])` / `Arr::except($array, [...])` |
| Chained map / filter / reduce on array | `collect($array)->filter(...)->map(...)->values()->all()` |

Other:

| If you reach for… | Use this instead |
|---|---|
| `exec()` / `shell_exec()` / backticks | `(new \Symfony\Component\Process\Process([$cmd, ...$args]))->mustRun()` |
| Manual YAML parsing | `\Symfony\Component\Yaml\Yaml::parse()` |
| JSON parsing without strict error handling | `json_decode($s, true, 512, JSON_THROW_ON_ERROR)` |
| UUID generation by `random_bytes` + hex shuffle | `Str::uuid()` or `\Symfony\Component\Uid\Uuid::v7()` |
| HTTP request to external service | Laravel's `\Http::get(...)` facade |

## Layering boundaries

Storm depends on Laravel + Symfony pieces. It must **not** depend on Winter modules. Conversely, Winter core modules are free to use Storm. So:

- CMS / theme / plugin / system concerns live in `modules/` (backend, cms, system).
- Generic filesystem, path, parser, network primitives live in `vendor/winter/storm/`.
- When a Storm class needs a *policy* that's CMS-specific (e.g. "which directories count as theme asset roots"), expose a public setter on the Storm class and have the module-level caller supply the policy. Don't reach into module-level constants from Storm.

## Autoloading & file placement

Modules and plugins are autoloaded by `Winter\Storm\Support\ClassLoader` (see `ClassLoader::load()`), **not** plain PSR-4. Its convention: the namespace's **directory segments are lower-cased** to form the path, while the class file keeps its proper PascalCase name. So `System\Twig\SecurityPolicy\SafeCollection` resolves to `modules/system/twig/securitypolicy/SafeCollection.php` — note the lowercase `securitypolicy/` directory.

- **New sub-namespace directories must be lowercase on disk**, even though the namespace segment stays PascalCase: `System\Twig\Node` → `modules/system/twig/node/`, not `Node/`. The file name keeps its PascalCase and must match the class name exactly.
- This only bites on case-sensitive filesystems: a capitalized directory works on macOS/Windows (case-insensitive) and passes local tests, then fails Linux CI with `Class "…" not found`. **If Windows CI is green but every Ubuntu job fails to find a class, suspect a directory-case mismatch.**

## Tests

- Backend/CMS/system tests usually extend `System\Tests\Bootstrap\PluginTestCase` (boots Laravel, plugins, auth) or `System\Tests\Bootstrap\TestCase` (boots the framework but not plugins).
- Fixtures that flow through `Assetic\Asset\FileAsset` (e.g. `CombineAssets::combineToFile()`) must live under `base_path()`. `sys_get_temp_dir()` will fail with "source is not in the root directory". Use `base_path('storage/framework/cache/<unique>')` for temp dirs and clean up with `\File::deleteDirectory()` in `tearDown()`.
- A failing test that doesn't reproduce on a fresh clone is almost always a stale local `vendor/`. Run `composer update` in Storm (`~/Repositories/WinterCMS/Core/storm` or wherever you check it out) before claiming "environment issue".

## Code style (phpcs) — run it before pushing

CI runs a **PHP code-quality** check (the fast `PHP` job, ~15s) that fails the whole PR on style violations, separate from the test matrix. Run the exact same check locally on your changed files before pushing — it's instant and saves a CI round-trip:

```
vendor/bin/phpcs -nq --report=full --extensions=php <changed .php files>
```

(CI's `.github/workflows/utilities/phpcs-pr` runs `phpcs` against just the files changed vs. the base branch, using the repo-root `phpcs.xml` ruleset.) The sniff that bites templates: **alternative-syntax control blocks must span multiple lines** — `<?php if ($x): ?>attr="…"<?php endif ?>` on one line fails ("Newline required after opening brace" / "Closing brace must be on a line by itself"); put the `if:`, body, and `endif` on their own lines (see `modules/backend/formwidgets/fileupload/partials/_file_single.php`).

## Working across Winter core + Storm

When a change touches both, work in the actual local checkouts (e.g. `~/Repositories/WinterCMS/Core/storm` and `~/Repositories/WinterCMS/Core/winter`) on parallel branches, and symlink `vendor/winter/storm` to the local Storm checkout so changes are visible in the live install. Avoid `/tmp` worktrees — the user can't test what they can't see.

Open both PRs concurrently; the maintainer handles merge order and Storm release tagging. The Winter core PR's `composer.json` constraint bump waits for the Storm tag.

## Developing with symlinked plugins

Plugins are commonly symlinked into `plugins/<author>/<name>` to develop them from their own checkout. Four things bite, in order of how much time they waste:

- **Assets 404 until mirrored.** When the docroot is a `public/` mirror, module/plugin assets are served from symlinks under `public/`. After adding or symlinking a plugin, run `php artisan winter:mirror public --relative` (then `php artisan winter:up` to migrate). Skip it and the plugin's assets 404 — and because a browser ORB-blocks a cross-origin script that comes back as `text/html` (the 404 page), **JS-driven UIs render blank with no console error**. If a plugin "loads but its editor/widget/builder area is empty", check the network tab for 404'd assets before suspecting the code.
- **Vite builds can't resolve the workspace's `node_modules`.** `vite:compile <Plugin>` loads the plugin's `vite.config.mjs` from the symlink's *real* path, so Node resolves `node_modules` upward from there and misses the project install (`@vitejs/plugin-vue`, `laravel-vite-plugin`, …). Bridge it with `ln -s <project>/node_modules <plugin-real-path>/node_modules` (gitignored), then `vite:install <Plugin>` / `vite:compile <Plugin>`. Commit the regenerated `assets/dist/`, not the generated `package-lock.json`. Vite also caches builds in `<plugin>/node_modules/.vite`, so a source edit can silently produce identical output — if `vite:compile` doesn't seem to reflect your change, `rm -rf <plugin-real-path>/node_modules/.vite` and recompile.
- **PackageManager once dropped symlinked packages entirely.** The asset `PackageManager` resolved each package to its realpath (outside `base_path()`), so Vite/Mix packages in symlinked plugins failed to register and never served. Fixed in `modules/system/classes/asset/PackageManager.php` (`registerPackage()` keeps the in-project path when a symlink resolves outside the project). Prefer `PathResolver::within()` / `PathResolver::resolve()` over hand-rolled `str_starts_with`/`realpath` when touching that path logic.
- **`vite:watch` (HMR) writes an unreachable hot-file URL on IPv6 hosts.** `php artisan vite:watch <Plugin>` runs a dev server with HMR — edits to `assets/src/**` reload live in the browser with no manual `vite:compile`/`winter:mirror`, which is by far the fastest loop for styling work. But it binds `--host` to all interfaces, so `laravel-vite-plugin` writes `http://[::]:5173` into `<plugin>/assets/dist/hot`, which a browser can't reach — the backend then loads unstyled. Fix after starting the watch: `echo -n 'http://localhost:5173' > <plugin>/assets/dist/hot`, then reload the page once so the backend serves from the dev server. Stopping the watch removes the hot file and recompiles `dist/`; verify it's gone afterwards or the backend keeps pointing at the dead dev server. Tracked upstream in wintercms/winter#1525. (This is the Vite pipeline's fast loop — see "Fast styling-iteration loop" below for the browser-driven half, which is the same across all three asset pipelines.)

## Fast styling-iteration loop (all three asset pipelines)

Iterating on backend styling via edit → recompile → `winter:mirror` → screenshot is slow, and headless screenshots also mislead (a headless synthetic click fires `:focus-visible` that a real mouse click doesn't). Two MCP servers make this dramatically faster — add them once with `claude mcp add <name> -s user -- npx <pkg>`:

- **`chrome-devtools` MCP** (`chrome-devtools-mcp`) — **recommended default.** Drives a persistent, logged-in **real** Chrome. `evaluate_script` inspects computed styles *and* drives the UI in a single call; `take_screenshot`/`take_snapshot` for visual/a11y checks. No throwaway `*.js` scripts, no browser relaunch between turns, no headless render/focus artifacts.
- **`playwright` MCP** (`@playwright/mcp`) — general browser automation (structured a11y snapshots + actions) for scripted multi-step flows rather than DevTools-style inspection.

The high-leverage habit is **live-inject-then-commit**: find CSS values by injecting a `<style>` via `evaluate_script`, eyeball it in the real page with zero compiles, and only write to source once it's right. If an injected rule works but disappears after a build, the *build* ate it — a much faster diagnosis than chasing specificity.

That browser half is **compiler-agnostic**. Only the step that turns a *source* edit into a page change differs by pipeline:

- **Vite** (`addVite`, e.g. Winter.TailwindUI): `php artisan vite:watch <Plugin>` → HMR, the edit appears live with no compile/mirror/reload. Fix its IPv6 hot-file bug first (see the symlinked-plugins section above / wintercms/winter#1525).
- **Mix / laravel-mix** (a `mix:compile` package, e.g. LukeTowers.EasyForms's formbuilder widget): `php artisan mix:watch <Plugin>` → recompiles to disk on save (no HMR), so **reload** the page to see it. On a mirror docroot the plugin dir is symlinked into `public/`, so no re-mirror is needed.
  - **Always scope with `-p`: `mix:compile -p <package>`.** A bare positional arg (`mix:compile luketowers.easyforms`) is treated as a webpack passthrough, *not* a package filter, so it compiles **every** registered Mix package — including `module-backend.formwidgets.codeeditor`. That build can fail (`ENOENT: js/build/codeeditor.bundle.js`) and **delete the committed `codeeditor.bundle.js` + chunks**, leaving the Monaco codeeditor broken everywhere (it silently fails to register with Snowboard). After any `mix:compile`, `git status` the build dirs and `git checkout -- <path>` to restore anything a failed sub-build wiped. Add `-f` for a production (minified) build matching committed output.
- **Asset combiner / LESS**: there is **no watch**. Precompiled module CSS (`modules/*/assets/css/*.css`, e.g. `winter.css`) needs an explicit `php artisan winter:util compile less` (see below), then reload. Plugin `CombineAssets` bundles (`.less`/`.js` arrays passed to `addCss`/`addJs`) recompile on the fly in dev, so editing source and reloading is enough.

Two traps in this loop cost real round-trips — worth internalising:

- **`getComputedStyle` read in the *same* script call that just changed a style is stale.** Injecting a `<style>` (or setting `element.style`) and reading `getComputedStyle(el).<prop>` in one `evaluate_script` returns the *pre-recalc* value — it will report `rgba(0, 0, 0, 0)` even for an inline `!important` that is actually applied. Don't conclude "my override lost the cascade" from that read. **Trust `take_screenshot`** (ground truth), or read the computed value in a *separate* call after styles settle. A whole "cross-origin `!important` is beating me" rabbit hole this session was just this stale read plus a leftover debug `<style>`.
- **Cache-busting an asset only works with a *fresh* `?v` string.** Compiled assets are versioned by a query param (`?v<system::core.build>` for core/module assets, `?v<plugin version>` for plugins). Bumping the param busts the browser cache *only if the value has never been served before* — restoring a **previously-used** value (e.g. back to canonical `1.2.14` after testing under `1.2.14-t5`) re-serves the stale copy the persistent browser context cached at that exact URL. To verify a rebuild, bump to a **fresh unique** value (`1.2.14-v2`); to confirm the *server* has the new file regardless of cache, `fetch(url, { cache: 'reload' })` and inspect the text. In production the natural version bump on release busts it everywhere; locally a hard-refresh also works.

## Never hand-edit compiled CSS

The `*.css` under `modules/*/assets/css/` (e.g. `modules/backend/assets/css/winter.css`) are **build artifacts** compiled from the LESS under `modules/*/assets/less/`. Edit the `.less` source and recompile — never patch the `.css` directly, or the next compile silently reverts you and reviewers can't trace the change to a source.

- Recompile with `php artisan winter:util compile less`.
- **Caveat:** that command recompiles *every* registered LESS package, including symlinked plugins. Some plugins ship a `.css` that concatenates vendored third-party CSS (e.g. a datepicker) which their `.less` does not reproduce — recompiling **truncates** those files. After a compile, `git status` the plugin repos and revert any clobbered vendored CSS (`git checkout -- assets/css/<file>.css`).
- Compiled output tracks the local `browserslist`; a stale one (the "caniuse-lite is N months old" warning) drifts vendor prefixes vs. the committed baseline. Run `npx update-browserslist-db@latest` if you need the diff to stay minimal.

## Backend skins (default vs TailwindUI)

The backend renders under a skin set by `cms.backendSkin` (default `Backend\Skins\Standard`). The **Winter.TailwindUI** plugin overrides it to its own skin in `boot()` (`Config::set('cms.backendSkin', …)`), restyling the whole backend — so a widget can look right under one skin and broken under the other (missing buttons, wrong spacing).

- Toggle it with the plugin: `art plugin:disable winter.tailwindui` / `art plugin:enable winter.tailwindui` (`art` = `php artisan`). Disabling drops back to the default Standard skin.
- When adjusting a **core** widget's styling (e.g. the `iconpicker` modal), do the first pass with **TailwindUI disabled** so you're tuning the core/default baseline, then re-enable it and fix whatever the skin layers on top (buttons not visible, etc.). Core style changes live in `modules/*/assets/less/**` — recompile with `winter:util compile less` (see "Never hand-edit compiled CSS").
