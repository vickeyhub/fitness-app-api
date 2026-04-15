# Phases: Admin Sessions, Bookings, Payments, Social/Content

This file tracks phased delivery of the **Classes (sessions)**, **Bookings**, **Payments**, and **Social/Content** admin UIs.

---

## Phase 0 — Foundation (done)

- [x] Shared CSRF meta + jQuery `$.ajaxSetup` pattern (already on admin layout / users).
- [x] Bootstrap 3 pagination for admin tables (`Paginator::useBootstrapThree()` in `AppServiceProvider`).
- [x] Route model binding for `{classes}` → `App\Models\Classes` (`Route::bind` in `AppServiceProvider`).
- [x] Model fixes: `Booking` uses `SoftDeletes`; `session()` is `belongsTo(Classes)`; `Classes::bookings()` is `hasMany`.
- [x] Global admin UI helpers restored and standardized: Select2 + Flatpickr assets + `window.initUiEnhancements()` in `layouts/admin.blade.php`.
- [x] Select2 z-index/modal overlap fixes applied (`dropdownParent: body` + z-index tuning) so dropdowns render above modal content.

---

## Phase 1 — Sessions (Classes) admin

**Goal:** Operators can list, create, edit, soft-delete sessions (`classes` table) without the mobile app.

- [x] `Admin\ClassesController`: `index`, `show` (JSON), `store`, `update`, `destroy`.
- [x] Validation aligned with API session shape: JSON arrays as strings, `session_timing` drives computed `duration`, trainer must be `user_type = trainer`.
- [x] Blade: `resources/views/admin/classes/index.blade.php` + `_modal_form.blade.php` + `_scripts.blade.php`.
- [x] Routes under `auth` middleware: `admin/classes` CRUD.
- [x] Sidebar link: **Sessions** → `admin.classes.index`.

**Follow-up (optional later):**

- [ ] Thumbnail upload to `public` disk (API supports file; admin currently uses URL/path string).
- [x] User-friendly session fields: `session_catalog_items` migration + checkboxes / day picker / step list; **Manage options** modal to extend catalog (`SessionCatalogController`).
- [x] Search/filter/pagination query params on index.
- [x] Sessions index filter UX: right-side show/hide filters toggle with query-preserving pagination.
- [x] Session detail modal upgraded to modern card UI and includes **Edit session** action from within the popup.

---

## Phase 2 — Bookings admin

**Goal:** Operators can see all bookings, create manual entries, edit status and payment linkage, soft-delete.

- [x] `Admin\BookingsController`: `index`, `show` (JSON), `store`, `update`, `destroy`.
- [x] Eager loads: user, trainer, gym, session, payment.
- [x] Empty optional FKs normalized to `null` before validation.
- [x] Blade: `resources/views/admin/bookings/index.blade.php` (modals + table + jQuery).
- [x] Routes: `admin/bookings` CRUD.
- [x] Sidebar: **Bookings**; submenu “All bookings” points to same index.

**Follow-up (optional later):**

- [x] Filters (date range, status, payment_status, user id, session, search) on index.
- [x] Pagination with query string retention.
- [x] Booking form time UX changed to separate `start_time` + `end_time` inputs; controller composes/stores normalized `time_slot` (`10:00am - 11:00am`).
- [x] Booking detail modal upgraded to concise modern card view (no raw JSON payload dump).
- [ ] Inline link from booking row to related user/session/payment admin screens.

---

## Phase 3 — Payments admin (read-only)

**Goal:** Finance/support can inspect Stripe-backed rows without mutating them from the panel.

- [x] `Admin\PaymentsController`: `index`, `show` (JSON).
- [x] Blade: `resources/views/admin/payments/index.blade.php` (table + view modal).
- [x] Routes: `GET admin/payments`, `GET admin/payments/{payment}`.
- [x] Sidebar: **Payments**.

**Follow-up (optional later):**

- [x] Filters (status, date, user, currency, amount range, search, per-page).
- [ ] External deep link to Stripe Dashboard using `payment_intent_id`.
- [x] Payment detail modal upgraded to structured modern UI (overview/user/gateway response) instead of raw JSON dump.
- [ ] No create/edit here by design; refunds/disputes stay in Stripe or a future service layer.

---

## Phase 4 — QA & hardening (recommended next)

- [ ] Manual test matrix: create session → create booking referencing session → confirm payment row exists from API flow; admin CRUD on session/booking does not break FKs.
- [ ] Role gate: restrict `admin/*` routes to `admin` / `super_admin` only (`auth` + policy or middleware).
- [ ] Handle DB integrity errors on delete (sessions with active bookings) with friendly JSON/toastr message.
- [ ] Add browser-level smoke checks for Select2/Flatpickr in all add/edit modals after dependency updates.

---

## Phase 5 — Social/Content moderation

**Goal:** Admin can moderate community content from web panel.

- [x] Added `Admin\PostsController` with list/filter/view/create/edit/delete endpoints.
- [x] Added `Admin\CommentsController` with global moderation list/filter/delete.
- [x] Added `Admin\StatusesController` with list/filter/delete and storage cleanup.
- [x] Added `Admin\TagsController` with list + create/update/delete.
- [x] Added routes under `auth` middleware: `admin/posts` (CRUD + like/comment actions), `admin/comments`, `admin/statuses`, `admin/tags`.
- [x] Added sidebar link to **Social/Content (Posts)** and dedicated management links for gyms/trainers.
- [x] Added views:
  - `resources/views/admin/posts/index.blade.php`
  - `resources/views/admin/comments/index.blade.php`
  - `resources/views/admin/statuses/index.blade.php`
  - `resources/views/admin/tags/index.blade.php`
- [x] Upgraded post popup to Instagram-like desktop layout with admin like/unlike and comment actions.
- [x] Added admin post edit flow (prefill modal + update endpoint) for post correction.

**Follow-up (optional later):**

- [ ] Add soft-delete restore workflow for posts if moderation policy requires undo.
- [ ] Add "hide vs delete" moderation state for posts/comments.
- [ ] Add direct jump links from post detail to comments and user profile drill-down.

---

## Phase 6 — Workouts & Exercises admin

**Goal:** Admin can manage exercise master data and inspect workout activity from web panel.

- [x] Added `Admin\ExerciseCategoriesController` with list/create/show/update/delete.
- [x] Added `Admin\ExercisesController` with list/filter/create/show/update/delete.
- [x] Added `Admin\WorkoutPlansController` (read-only oversight with user/name filters and exercise line preview).
- [x] Added `Admin\WorkoutLogsController` (read-only oversight with user/type/date filters).
- [x] Added routes under `auth` middleware:
  - `admin/exercise-categories` CRUD
  - `admin/exercises` CRUD
  - `admin/workout-plans` (index)
  - `admin/workout-logs` (index)
- [x] Added sidebar **Workouts & Exercises** nav group with links for categories, exercises, plans, logs.
- [x] Added views:
  - `resources/views/admin/exercise-categories/index.blade.php`
  - `resources/views/admin/exercises/index.blade.php`
  - `resources/views/admin/workout-plans/index.blade.php`
  - `resources/views/admin/workout-logs/index.blade.php`
- [x] Updated model fillables for `ExerciseCategory` and `Exercise` to support admin CRUD.

**Follow-up (optional later):**

- [ ] Add dedicated drill-down modal/page for `exercise_logs` linked by `workout_id`.
- [ ] Add bulk import tools for exercises and categories.
- [ ] Add soft-delete/restore workflow if product policy requires archive behavior.

---

## Files touched (reference)

| Area | Files |
|------|--------|
| Routes | `routes/web.php` |
| Controllers | `app/Http/Controllers/Admin/ClassesController.php`, `BookingsController.php`, `PaymentsController.php`, `PostsController.php`, `CommentsController.php`, `StatusesController.php`, `TagsController.php`, `UsersController.php`, `ExerciseCategoriesController.php`, `ExercisesController.php`, `WorkoutPlansController.php`, `WorkoutLogsController.php` |
| Views | `resources/views/admin/classes/*`, `admin/bookings/index.blade.php`, `admin/payments/index.blade.php`, `admin/posts/index.blade.php`, `admin/comments/index.blade.php`, `admin/statuses/index.blade.php`, `admin/tags/index.blade.php`, `admin/exercise-categories/index.blade.php`, `admin/exercises/index.blade.php`, `admin/workout-plans/index.blade.php`, `admin/workout-logs/index.blade.php` |
| Nav | `resources/views/layouts/nav.blade.php` |
| Models | `app/Models/Booking.php`, `app/Models/Classes.php` |
| Provider | `app/Providers/AppServiceProvider.php` |

---

*Status: Phases 0–3, 5, and 6 implemented. Phase 4 remains checklist for hardening and QA.*
