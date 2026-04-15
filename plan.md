# Admin panel build plan

This document maps your **database schema (migrations)**, **REST API (`routes/api.php`)**, and **web admin (`routes/web.php` + `App\Http\Controllers\Admin`)** to what should exist in the admin panel. It is planning only—no implementation steps are executed here.

---

## Progress update (implemented)

- **Users:** Full admin CRUD with profile fields, jQuery modal workflows, plus separate management entry points for **All Users**, **Trainer Management**, and **Gym Management**.
- **Sessions (classes):** CRUD, trainer-linked creation, catalog-backed checkboxes (muscles/goals/types/keywords), session filters + pagination, modern detail popup with inline edit action.
- **Session catalog:** New `session_catalog_items` table + admin endpoints/modal for managing selectable options.
- **Bookings:** CRUD, filters + pagination, separate `start_time`/`end_time` form inputs with controller-side normalized `time_slot`, modern detail popup.
- **Payments:** Read-only index + detail, filters + pagination, structured modal view (no raw payload dump as primary UI).
- **Social/Content:** Posts/Comments/Statuses/Tags modules implemented; posts now support admin create, edit, delete, like/unlike, and comment actions from a modern popup.
- **Moderation hardening:** Added hide/unhide state for posts/comments/statuses and restore flow for soft-deleted posts.
- **Workouts & Exercises (2.5):** Exercise categories CRUD, exercises CRUD, workout plans/logs CRUD, and exercise logs oversight are implemented.
- **Nutrition:** Meals/targets CRUD plus adherence report (target vs consumed) is implemented.
- **Audit + consistency hardening:** Audit trail infra added for critical mutations; workout type normalization and nutrition target upsert parity applied.
- **Dashboard 2.0:** Real KPI cards/charts implemented from existing tables (bookings, revenue, active users, content volume, workout logs, nutrition adherence).
- **UI foundation:** Select2 + Flatpickr integrated globally in admin with modal stacking/z-index fixes.

---

## 1. Current admin vs API

| Area | Admin (web) today | API coverage |
|------|-------------------|--------------|
| Dashboard | User aggregates + chart endpoint (`DashboardController`) | N/A (app-facing) |
| Users | Full CRUD + profile fields + separate trainer/gym management views (`Admin\UsersController`) | Profile update, list users, trainers, buddy search (`UserController`) |
| Sessions (classes) | CRUD + filtering + modern detail + catalog management | Sessions/search/filter endpoints |
| Bookings | CRUD + filtering + modern detail | Booking list/create endpoints |
| Payments | Read-only listing + filtering + structured detail view | Payment + webhook pipeline |
| Social/content | Posts + comments + statuses + tags moderation, post create/edit, admin like/comment flows | Rich API coverage exists |
| Workouts & exercises | Exercise categories/exercises CRUD, workout plans/logs CRUD, exercise log oversight | API coverage exists |
| Nutrition | Meals CRUD, targets CRUD, adherence analytics | Rich API coverage exists |

**Gap:** Core operations and moderation are operational in admin. Remaining major gap is device token support tools and role-based authorization hardening.

---

## 2. Schema inventory (from migrations)

### 2.1 Core identity & billing

- **`users`** — `first_name`, `last_name`, `username`, `email`, `mobile_number`, `password`, `user_type` (includes `super_admin` after `2026_04_15_000000_add_super_admin_to_users_user_type` on MySQL), `otp`, `status`, `paid_status`, soft deletes; later: `stripe_customer_id`, `getstream_user_id`.
- **`user_profile`** — profile picture, age, weight/height (+ units), gender, dob, location, rating, specialties, trainer fields, etc. (via follow-up migrations).
- **`payments`** — Stripe-oriented records: `user_id`, customer/email/name, `payment_intent_id`, status, amount, currency, `payment_method`, `response_data` (JSON).
- **`device_tokens`** — push: `user_id`, `player_id` (unique).

### 2.2 Sessions / marketplace (named `classes` in DB)

- **`classes`** — Trainer-owned sessions: title, description, duration, calories, JSON `steps` / `muscles_involved` / `schedule`, `user_id` (trainer), price, thumbnail, rating, timing; plus `session_type`, `session_keywords`, `fitness_goal`, `intensity`; plus `is_publish`, `latitude`, `longitude`, `radius`. Soft deletes.
- **`bookmarks`** — `user_id` + `session_id` (→ `classes`).

### 2.3 Bookings

- **`bookings`** — `user_id`, `trainer_id`, `gym_id`, `session_id`, `payment_id`, `booking_date`, `time_slot`, `status` (cancelled / confirmed / pending), `payment_status`, soft deletes.

### 2.4 Social / content

- **`posts`** — title, description, thumbnail, `user_id`; soft deletes.
- **`comments`** — `post_id`, `user_id`, `comment`.
- **`likes`** — `post_id`, `user_id`, `type` (`like` | `dislike`).
- **`tags`**, **`post_tag`** — tagging for posts.
- **`follows`** — follower / following user pairs (unique pair).
- **`statuses`** — short-lived media feed: `user_id`, `type` (photo/video), `media`, `caption`.

### 2.5 Workouts & exercises

- **`exercise_categories`** — name (unique).
- **`exercises`** — name, `exercise_category_id`, description.
- **`workout_plans`** — `user_id`, name.
- **`workout_plan_exercises`** — plan ↔ exercise with sets, reps, `rest_seconds`, weight.
- **`workout_logs`** — `user_id`, `workout_id`, `workout_type`, start/end, duration, calories, notes.
- **`exercise_logs`** — per-exercise log lines keyed by `workout_id` string (no FK in migration).

### 2.6 Nutrition

- **`meals`** — per user/date/meal_type macros + calories; unique `(user_id, date, meal_type)`.
- **`nutrition_targets`** — per-user calorie/protein/fat/carb targets.

### 2.7 Framework / infra (optional in admin)

- **`personal_access_tokens`**, **`cache`**, **`jobs`**, Laravel **`sessions`** table, **`password_reset_tokens`** — usually no product admin screens unless you need ops tooling.

---

## 3. API surface (what the app can do)

Derived from `routes/api.php` and `App\Http\Controllers\Api\*`.

### 3.1 Auth & account

- **AuthController** — `signup`, `verify-otp`, `login`, `logout` (Sanctum).
- **PasswordResetController** — forgot / reset password.

### 3.2 Users & discovery

- **UserController** — profile show/update; list users; trainers listing; find buddy.
- **FollowController** — follow / unfollow, following, followers, is-following.
- **GymsController** — list gyms (public), gym owner bookings (`owner-bookings`).

### 3.3 Sessions (classes) & bookmarks

- **SessionsController** — list (auth), search (public), detail (public), CRUD for trainer/gym owner flows, bookmarks, active plans fetch.
- **SessionFilterController** — filter metadata for sessions.

### 3.4 Bookings & payments

- **BookingsController** — list, create.
- **PaymentController** — create payment intent, confirm payment (Stripe).
- **StripeWebhookController** — webhook handler (not admin UI, but admin may need payment logs).

### 3.5 Workouts & exercises

- **ExerciseController** — categories, exercises by category.
- **WorkoutPlanController** — list / create / show / destroy plans.
- **WorkoutLogController** — log workout, log exercises, history.

### 3.6 Nutrition

- **NutritionController** — meals CRUD-by-date, targets get/set.

### 3.7 Social

- **PostController** — full API resource for posts.
- **LikeController** — like / dislike.
- **CommentController** — list, create, delete comments.
- **TagController** — list, create tags.
- **StatusController** — upload status media, feed, mine, delete.

### 3.8 Chat & notifications

- **GetStreamController** — token, channel, bulk register users.
- **NotificationController** — POST `/notification` (push pipeline).

### 3.9 Misc

- Public **gyms** list, **session-detail**, **search-sessions**, **stripe/webhook**.

---

## 4. Recommended admin panel modules (prioritized)

Use the same stack as today: **Blade + Inspinia-style layout + jQuery + server routes** (or later Livewire/Inertia if you refactor). Each module should align with the tables above and mirror what support/ops would need when debugging API issues.

### P0 — Operations & revenue

1. **Bookings management** — ✅ Implemented (CRUD + filtering + modern detail). Optional: audit trail, cross-links.
2. **Sessions / classes management** — ✅ Implemented (CRUD + publish controls + filtering + catalog-driven UX + modern detail).
3. **Payments** — ✅ Implemented read-only list/detail with filtering. Optional: Stripe dashboard deep-link.

### P1 — Content moderation

4. **Posts** — ✅ Implemented list/search/view/create/edit/delete moderation screen (`admin/posts`) with popup interactions.
5. **Comments** — ✅ Implemented global moderation list + filters + delete (`admin/comments`).
6. **Statuses** — ✅ Implemented list/filter/delete moderation screen with media open link (`admin/statuses`).
7. **Follows** — ✅ Implemented support moderation list/filter/remove screen (`admin/follows`).
8. **Tags** — ✅ Implemented CRUD screen with modal create/edit + delete (`admin/tags`).

### P2 — Catalog & fitness data

8. **Exercise categories** — ✅ Implemented CRUD `exercise_categories`.
9. **Exercises** — ✅ Implemented CRUD `exercises` per category (aligned with `ExerciseController`).
10. **Workout oversight** — ✅ Implemented full `workout_plans` and `workout_logs` management plus `exercise_logs` oversight with filters.

### P3 — Users & engagement depth

11. **User drill-down** — Extend current user admin: linked **bookings**, **posts**, **payments**, **plans**, **nutrition** summaries (read-only tabs).
12. **Follows** — ✅ Implemented list/filter/remove flow for disputes/abuse support.
13. **Likes** — ✅ Admin like/unlike is available in post detail popup; standalone module still optional.

### P4 — Nutrition & devices

14. **Meals / targets (support)** — ✅ Implemented CRUD support tools for `meals` / `nutrition_targets` plus adherence view.
15. **Device tokens** — List/clear `device_tokens` for a user when debugging push (`NotificationController`).

### P5 — Dashboard hardening

16. **Dashboard metrics** — ✅ Implemented KPI + chart layer from existing data. Remaining optional enhancement: publish/draft session ratio and explicit `super_admin`/`admin` split cards.

### P6 — Integrations (usually config, not CRUD)

17. **GetStream** — Admin page for env status + “re-sync user” calling same logic as `GetStreamController` (optional).
18. **Stripe** — Webhook health + last events (optional; mostly logs and Stripe Dashboard).

---

## 5. Cross-reference: entity → primary API → admin goal

| Entity (table) | Primary API controllers | Admin goal |
|----------------|-------------------------|------------|
| users / user_profile | Auth, User | Done: user CRUD; extend with related tabs |
| classes | Sessions, SessionFilter | Session catalog & moderation |
| bookings | Bookings | Booking ops console |
| payments | Payment, Stripe webhook | Payment visibility & reconciliation aid |
| posts, comments, likes, tags | Post, Comment, Like, Tag | Moderation & tag hygiene |
| follows | Follow | Support / abuse view + relation cleanup |
| statuses | Status | Media moderation |
| exercise_categories, exercises | Exercise | Master data admin |
| workout_plans, workout_plan_exercises | WorkoutPlan | User plan inspection (+ optional edit) |
| workout_logs, exercise_logs | WorkoutLog | Support timelines + admin management |
| meals, nutrition_targets | Nutrition | Support CRUD + adherence analytics |
| device_tokens | Notification | Push debugging |

---

## 6. Implementation notes (non-functional)

- **Authorization:** Web routes use `auth` only; consider **role checks** (`super_admin` / `admin`) before exposing destructive actions.
- **Parity:** Admin actions that duplicate API rules should **reuse validation** or **call services** shared with API controllers to avoid drift.
- **Naming:** API uses “sessions”; DB table is **`classes`**—keep naming consistent in UI labels (“Sessions / Classes”).
- **Dashboard SQL:** After `super_admin` enum change, dashboard counts may want an explicit bucket for `super_admin` alongside `admin` if you separate staff roles in charts.

---

## 7. Suggested order of execution

1. Add **role gates** for all admin routes (`admin`/`super_admin`) and policy-level checks on destructive actions.  
2. Add **device token** support tools for push-debugging.  
3. Add user drill-down cards/tabs on the user page (instead of only quick links).  
4. Integrations polish (GetStream/Stripe ops pages as needed).  
5. Extend reporting/export suite (invoices, booking finance reports, CSV/PDF).

---

*Generated from repository migrations (`database/migrations`), `routes/api.php`, `routes/web.php`, and `App\Http\Controllers\Admin` + `App\Http\Controllers\Api` as of the plan authoring date.*
