# Phases: Admin Sessions, Bookings, Payments

This file tracks phased delivery of the **Classes (sessions)**, **Bookings**, and **Payments** admin UIs. Scope matches `plan.md` P0 priorities.

---

## Phase 0 — Foundation (done)

- [x] Shared CSRF meta + jQuery `$.ajaxSetup` pattern (already on admin layout / users).
- [x] Bootstrap 3 pagination for admin tables (`Paginator::useBootstrapThree()` in `AppServiceProvider`).
- [x] Route model binding for `{classes}` → `App\Models\Classes` (`Route::bind` in `AppServiceProvider`).
- [x] Model fixes: `Booking` uses `SoftDeletes`; `session()` is `belongsTo(Classes)`; `Classes::bookings()` is `hasMany`.

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
- [ ] Search/filter/pagination query params on index.

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

- [ ] Filters (date range, status, payment_status, user id).
- [ ] Pagination with query string retention.
- [ ] Inline link from booking row to related user/session/payment admin screens.

---

## Phase 3 — Payments admin (read-only)

**Goal:** Finance/support can inspect Stripe-backed rows without mutating them from the panel.

- [x] `Admin\PaymentsController`: `index`, `show` (JSON).
- [x] Blade: `resources/views/admin/payments/index.blade.php` (table + view modal).
- [x] Routes: `GET admin/payments`, `GET admin/payments/{payment}`.
- [x] Sidebar: **Payments**.

**Follow-up (optional later):**

- [ ] Filters (status, date, user).
- [ ] External deep link to Stripe Dashboard using `payment_intent_id`.
- [ ] No create/edit here by design; refunds/disputes stay in Stripe or a future service layer.

---

## Phase 4 — QA & hardening (recommended next)

- [ ] Manual test matrix: create session → create booking referencing session → confirm payment row exists from API flow; admin CRUD on session/booking does not break FKs.
- [ ] Role gate: restrict `admin/*` routes to `admin` / `super_admin` only (`auth` + policy or middleware).
- [ ] Handle DB integrity errors on delete (sessions with active bookings) with friendly JSON/toastr message.

---

## Files touched (reference)

| Area | Files |
|------|--------|
| Routes | `routes/web.php` |
| Controllers | `app/Http/Controllers/Admin/ClassesController.php`, `BookingsController.php`, `PaymentsController.php` |
| Views | `resources/views/admin/classes/*`, `admin/bookings/index.blade.php`, `admin/payments/index.blade.php` |
| Nav | `resources/views/layouts/nav.blade.php` |
| Models | `app/Models/Booking.php`, `app/Models/Classes.php` |
| Provider | `app/Providers/AppServiceProvider.php` |

---

*Status: Phases 0–3 implemented in codebase; Phase 4 is checklist for you to run through when ready.*
