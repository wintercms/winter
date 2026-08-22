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

## Working across Winter core + Storm

When a change touches both, work in the actual local checkouts (e.g. `~/Repositories/WinterCMS/Core/storm` and `~/Repositories/WinterCMS/Core/winter`) on parallel branches, and symlink `vendor/winter/storm` to the local Storm checkout so changes are visible in the live install. Avoid `/tmp` worktrees — the user can't test what they can't see.

Open both PRs concurrently; the maintainer handles merge order and Storm release tagging. The Winter core PR's `composer.json` constraint bump waits for the Storm tag.

## Developing with symlinked plugins

Plugins are commonly symlinked into `plugins/<author>/<name>` to develop them from their own checkout. Four things bite, in order of how much time they waste:

- **Assets 404 until mirrored.** When the docroot is a `public/` mirror, module/plugin assets are served from symlinks under `public/`. After adding or symlinking a plugin, run `php artisan winter:mirror public --relative` (then `php artisan winter:up` to migrate). Skip it and the plugin's assets 404 — and because a browser ORB-blocks a cross-origin script that comes back as `text/html` (the 404 page), **JS-driven UIs render blank with no console error**. If a plugin "loads but its editor/widget/builder area is empty", check the network tab for 404'd assets before suspecting the code.
- **Vite builds can't resolve the workspace's `node_modules`.** `vite:compile <Plugin>` loads the plugin's `vite.config.mjs` from the symlink's *real* path, so Node resolves `node_modules` upward from there and misses the project install (`@vitejs/plugin-vue`, `laravel-vite-plugin`, …). Bridge it with `ln -s <project>/node_modules <plugin-real-path>/node_modules` (gitignored), then `vite:install <Plugin>` / `vite:compile <Plugin>`. Commit the regenerated `assets/dist/`, not the generated `package-lock.json`. Vite also caches builds in `<plugin>/node_modules/.vite`, so a source edit can silently produce identical output — if `vite:compile` doesn't seem to reflect your change, `rm -rf <plugin-real-path>/node_modules/.vite` and recompile.
- **PackageManager once dropped symlinked packages entirely.** The asset `PackageManager` resolved each package to its realpath (outside `base_path()`), so Vite/Mix packages in symlinked plugins failed to register and never served. Fixed in `modules/system/classes/asset/PackageManager.php` (`registerPackage()` keeps the in-project path when a symlink resolves outside the project). Prefer `PathResolver::within()` / `PathResolver::resolve()` over hand-rolled `str_starts_with`/`realpath` when touching that path logic.
- **`vite:watch` (HMR) writes an unreachable hot-file URL on IPv6 hosts.** `php artisan vite:watch <Plugin>` runs a dev server with HMR — edits to `assets/src/**` reload live in the browser with no manual `vite:compile`/`winter:mirror`, which is by far the fastest loop for styling work. But it binds `--host` to all interfaces, so `laravel-vite-plugin` writes `http://[::]:5173` into `<plugin>/assets/dist/hot`, which a browser can't reach — the backend then loads unstyled. Fix after starting the watch: `echo -n 'http://localhost:5173' > <plugin>/assets/dist/hot`, then reload the page once so the backend serves from the dev server. Stopping the watch removes the hot file and recompiles `dist/`; verify it's gone afterwards or the backend keeps pointing at the dead dev server. Tracked upstream in wintercms/winter#1525. Pair this with the `chrome-devtools` MCP (a persistent, logged-in real browser) to inspect computed styles and live-inject candidate CSS without throwaway scripts — far faster and more faithful than headless screenshots.

## Never hand-edit compiled CSS

The `*.css` under `modules/*/assets/css/` (e.g. `modules/backend/assets/css/winter.css`) are **build artifacts** compiled from the LESS under `modules/*/assets/less/`. Edit the `.less` source and recompile — never patch the `.css` directly, or the next compile silently reverts you and reviewers can't trace the change to a source.

- Recompile with `php artisan winter:util compile less`.
- **Caveat:** that command recompiles *every* registered LESS package, including symlinked plugins. Some plugins ship a `.css` that concatenates vendored third-party CSS (e.g. a datepicker) which their `.less` does not reproduce — recompiling **truncates** those files. After a compile, `git status` the plugin repos and revert any clobbered vendored CSS (`git checkout -- assets/css/<file>.css`).
- Compiled output tracks the local `browserslist`; a stale one (the "caniuse-lite is N months old" warning) drifts vendor prefixes vs. the committed baseline. Run `npx update-browserslist-db@latest` if you need the diff to stay minimal.
