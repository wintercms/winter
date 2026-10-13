# Calendar widget (PR #970) — session handover

_Working notes for picking this back up. **Delete this file before merge.**_

Branch: `wip/calendar-widget` (winter core) · tip when written: `170649685 wip`
Companion: `wintercms/wn-test-plugin` branch `wip/calendar-events` → **PR #25**
Core PR: **wintercms/winter#970** — un-drafted, CI green, ready for review.

---

## TL;DR

The widget is **working end-to-end and browser-verified**. The roadmap in #970's comment is done except the two explicitly-optional items (full de-vendor of FullCalendar into the build; a broader in-repo Dusk suite). The PHP surface stayed stable as intended; the real work was assets, the Snowboard/v6 JS port, recurrence/timezone, tests, docs — plus fixing several latent bugs the browser pass surfaced.

Automated coverage: **33 tests green** — 15 `CalendarWidgetTest` + 4 `CalendarControllerTest` + 9 `EventDataTest` (core) + 5 `EventCalendarTest` (test plugin).

Screenshots + a screen recording of the working widget: `~/Repositories/WinterCMS/Core/calendar-pr-screenshots/` (`01-month-view.png` … `08-event-edit-form.png`, `calendar-walkthrough.webm`).

---

## Commits (this session, on top of the original PR)

| Commit | What |
|---|---|
| `d4d034edc` | Remove dead FullCalendar **v4** `packages/*` tree (~90 files) |
| `fe9e8fe46` | Recurrence (#3): `applyDateRangeFilter` opt-out + `setApplyDateRangeFilter()` + tests |
| `a0e5b1016` | Timezone (#4): `timezone` config → event output + `data-timezone` + tests |
| `ef8355bc4` | Baseline tests: EventData, cacheKey stability, the 4 extension events |
| `1721b3d38` | `CalendarController` behavior tests |
| `b280cfbfb` | Docs: config options, recurrence patterns, real click-handler examples |
| `4fa3f9212` | **JS → Snowboard port** + FullCalendar v6 API fixes |
| `dbf1985ad` | Pass the visible window (`$startTime`,`$endTime`) to `extendQueryBefore`/`extendQuery` |
| `a09939620` | Core: keep point events with a null end in the window filter |
| `0d37bd02a` | **Snowboard port bug-fixes found in browser testing** (see below) |
| `c851e192a` | phpcs autofixes (style only) |

Added on the branch **after** my session (by you / other work): `4f6e440b1` NestedForm fix (#1522/#1523), `cf3d94b0e` merge develop, `170649685 wip` (a LESS→CSS recompile of `calendar.css` — colour casing + corrected `.tooltip-arrow` `left:calc(50% - 5px)`).

---

## Key decisions

- **Build = laravel-mix, not Vite.** The roadmap said "Vite pipeline" but this module builds JS/LESS with `modules/backend/winter.mix.js`. The calendar bundle compiles `assets/js/src/*.js` → `assets/js/dist/calendar.js`, registered in `winter.mix.js` next to the other widgets. `loadAssets()` serves `js/dist/calendar.js`.
- **FullCalendar stays vendored (v6.1.15)** at `assets/vendor/fullcalendar/*`; the plugin uses the `FullCalendar` global. Full de-vendor into npm+mix was deliberately **skipped** — the dead v4 tree (the big diff win) is already gone; the remaining bundle is the real library, so de-vendoring is marginal diff-for-risk. Optional follow-up.
- **Recurrence = server-side (roadmap option b), default-safe.** `applyDateRangeFilter` (config, default `true`) + `setApplyDateRangeFilter(false)` let a consumer keep recurring masters in the query and expand them in `backend.calendar.extendRecords`. Additionally `extendQueryBefore`/`extendQuery` now receive the visible window so consumers can write an efficient recurrence-aware query (`window-intersecting OR has-rrule`). Client-side `rrule` (option a) is documented as an alternative.
- **Timezone**: `timezone` config controls event output (offset-qualified ISO) and is surfaced via `data-timezone`; defaults to `app.timezone`. `moment-timezone` dropped (was in the deleted v4 tree); v6 handles local/UTC natively — **named zones need a FC named-tz plugin** we don't ship (documented).
- **Snowboard port keeps the month-window cache** (`CalendarCache`, moved to an ES module) and bridges to the **still-Storm** toolbar-search/filter widgets via jQuery framework events.

---

## Bugs found & fixed in browser testing (the important part)

All were latent in the original port; the browser pass surfaced them.

1. **Month cache timezone mismatch** (`CalendarCache.getMonthRequestData`) — computed day-of-week / month boundaries with the *browser's* local zone while FullCalendar reports timestamps in the *calendar's* zone. When they differ the "is this a month grid?" check failed and snapped to the wrong 42-day window → FullCalendar got the wrong month's events. Now does the math in the calendar's frame (UTC when the calendar runs in UTC).
2. **Point events (null end) dropped** — twice: the SQL filter (`recordEnd >= start` is NULL) and the client bucketing (`Date.parse(undefined)` = NaN). Both now treat a missing end as ending at the start. Core fix in `applyDateRangeToQuery`; JS fix in `saveFirstThreeMonthsData`.
3. **Search/filter wired to the wrong framework events** — the Storm framework fires **`oc.beforeRequest`** (not `wn.beforeRequest`), so the current month window was never injected into search/filter requests and recurring events vanished on search; and the onRefresh payload arrives via **`ajaxSuccess`** (`[context, data, …]`), not the jQuery-native `ajaxComplete`. `onFilterUpdate` now scans its args for the payload rather than assuming a position.

After these: month/week/day/list, month paging + cache, event click → edit form, search, all-day & date-range filtering, and recurrence expansion all work with **zero console errors**.

---

## How to build the JS/CSS

```bash
cd modules/backend
export PATH="$(git rev-parse --show-toplevel)/node_modules/.bin:$PATH"
# whole backend bundle (Snowboard, widgets, …):
mix --production            # or: mix   (dev, unminified)
```
To build **only** the calendar during iteration (avoids rebuilding every widget's dist), use a throwaway config:
```bash
cat > calendar.mix.js <<'JS'
const mix = require('laravel-mix');
mix.setPublicPath(__dirname);
mix.js('./widgets/calendar/assets/js/src/Calendar.js', './widgets/calendar/assets/js/dist/calendar.js');
JS
mix --production --mix-config=calendar.mix.js
rm -f calendar.mix.js mix-manifest.json      # mix-manifest.json is NOT tracked — don't commit it
```
`calendar.css` is compiled from `assets/less/calendar.less` (registered as a LESS bundle in `ServiceProvider::registerAssetBundles`).

## How to run the tests

Use the **root** phpunit config, not `modules/backend/phpunit.xml` — the backend config declares no bootstrap, so the Winter ClassLoader isn't registered and new fixtures (`CalendarEventFixture`) fail to autoload.
```bash
vendor/bin/phpunit modules/backend/tests/widgets/CalendarWidgetTest.php
vendor/bin/phpunit modules/backend/tests/widgets/EventDataTest.php
vendor/bin/phpunit modules/backend/tests/behaviors/CalendarControllerTest.php
vendor/bin/phpunit plugins/winter/test/tests/EventCalendarTest.php
```
Run **phpcs before pushing** (CI's "PHP" job): `vendor/bin/phpcs -nq --extensions=php <changed .php files>`; `vendor/bin/phpcbf` autofixes. The original calendar code had 31 style violations (inline control structures, spacing).

## How to browser-test

- Site: `https://winter.test` (Herd, MySQL `winter.test`). Backend admin created for automation: **`claude-test` / `ClaudeTest1234`** (superuser — delete when done).
- **Serving quirk:** the docroot is a Winter **public mirror** (`public/` = symlinks). After adding a *new* directory (e.g. the calendar widget dir or a new plugin), run **`php artisan winter:mirror public`** or its assets 404 (served as HTML → Chromium `ERR_BLOCKED_BY_ORB`, silent).
- Playwright is installed (chromium + ffmpeg). Reusable helper + the scripts I used are in this session's scratchpad (`pw-lib.js`, `cal-*.js`) — they log in, drive views/search/filter, and record video/screenshots. Run node with `NODE_PATH=<repo>/node_modules`. (Memory also notes a chrome-devtools-MCP + `vite:watch` HMR loop as the preferred fast loop for *other* assets, but the calendar bundle is mix, so rebuild with `mix` between JS edits.)

Seed demo data (August 2026, incl. recurring masters that start before the window):
```bash
php artisan tinker   # then create Winter\Test\Models\Event rows; see EventCalendarTest for shapes
```

---

## Test plugin (wn-test-plugin, PR #25)

Adds the fixture the calendar was verified against (supersedes the closed draft #21):
- `models/Event.php` — `winter_test_events` + a minimal RRULE expander `expandOccurrences()` (`FREQ=DAILY/WEEKLY/MONTHLY`, `INTERVAL`, `COUNT`, `UNTIL`; UNTIL compared by day).
- `controllers/Events.php` + `controllers/events/*` — CalendarController + List/Form, toolbar, search, all-day + date-range filter.
- `Plugin::boot()` wires the recommended server-side recurrence pattern for Event calendars.
- `tests/EventCalendarTest.php`, a migration, and a nav item.

Gotchas for that repo:
- Its `origin` is **SSH** (no key here) — I pushed over HTTPS: `git push https://github.com/wintercms/wn-test-plugin.git wip/calendar-events`.
- Your unrelated WIP there (Record.php, `v2.2.0/` translate-demo migration) was **left untouched** — I staged only calendar files and committed `version.yaml` with *only* my `2.3.0` entry via `git update-index --cacheinfo` so your `2.2.0` stayed uncommitted. If you switch that plugin back to `main`, your WIP restores cleanly.

---

## CI notes

- **PHP** (phpcs) is green after `c851e192a`. **JavaScript** (eslint) is green — eslint ignores the widget `assets/` paths by pattern, so the ported JS isn't linted.
- **Sub-split** flaked once (`splitsh-lite` >60s splitting `modules/system`) then passed on retry — infra timeout, not a code issue.
- CodeRabbit runs now that the PR is un-drafted (was skipped while draft).

---

## Remaining / follow-ups

- [ ] (optional) De-vendor FullCalendar v6 into the mix build; pull locales from `@fullcalendar/core/locales-all`.
- [ ] (optional) Broader in-repo Dusk e2e (PR #25 is the fixture).
- [ ] Named-timezone display needs a FullCalendar named-tz plugin if you want non-local/UTC zones rendered correctly.
- [ ] Review the `170649685 wip` CSS recompile and fold it into a real commit / squash before merge.
- [ ] The `recordColor` docblock in `Calendar.php` still says "the default background color in the calendar.less" — fine, just noting.

## File map

- Behavior: `modules/backend/behaviors/CalendarController.php` (+ `calendarcontroller/partials/_container.php`, `docs/example.*`)
- Widget: `modules/backend/widgets/Calendar.php`, `widgets/calendar/classes/EventData.php`, `widgets/calendar/partials/_calendar.php`
- JS: `widgets/calendar/assets/js/src/{Calendar,CalendarCache}.js` → `dist/calendar.js`
- Styles: `widgets/calendar/assets/less/calendar.less` → `css/calendar.css`
- Registration: `modules/backend/ServiceProvider.php`, `modules/backend/winter.mix.js`
- Tests: `modules/backend/tests/{widgets/CalendarWidgetTest.php,widgets/EventDataTest.php,behaviors/CalendarControllerTest.php,fixtures/models/CalendarEventFixture.php}`
