# Client Panel (Web) - Full Proof Delivery Plan

This document is the execution blueprint to build a production-ready web client panel on top of the current API-first backend (currently mobile-first usage).

Primary outcome: all critical end-user journeys must be usable from web without depending on mobile app screens.

---

## 1. Product scope and success criteria

### 1.1 Scope

- Build role-aware web experiences for:
  - `user`
  - `trainer`
  - `gym`
- Reuse existing API endpoints and data contracts.
- Keep server as source of truth for validation, authorization, and business rules.

### 1.2 Success criteria (must pass)

- A new user can signup/login, browse sessions, book, pay, and view booking/payment history on web.
- Trainer/gym users can manage their sessions and see related bookings on web.
- Workouts, nutrition, and social modules are available with parity to API capabilities.
- All sensitive actions are role-protected and auditable.

---

## 2. Delivery principles (non-negotiable)

- **API-first parity:** No web-only business rules unless explicitly approved.
- **Backward compatibility:** Existing mobile payloads/flows must not break.
- **Progressive rollout:** Ship in phases behind feature flags where needed.
- **Resilience:** Error handling, retries, and empty-state UX are required.
- **Observability:** Critical actions must be traceable in logs/audit entries.

---

## 3. Role model and permissions matrix

### 3.1 User

- Can view/edit own profile.
- Can discover sessions, bookmark, book, and pay.
- Can view own bookings, workouts, nutrition data, social interactions.
- Cannot manage others' sessions/bookings except allowed social actions.

### 3.2 Trainer

- Everything user can do (except restricted by policy), plus:
- Create/update/delete own sessions.
- View bookings relevant to own sessions.
- Manage own training-related content.

### 3.3 Gym

- Gym-facing session lifecycle.
- Gym booking visibility and related operational workflows.
- Limited user management only where API allows.

### 3.4 Security enforcement points

- Route middleware (`auth` + role middleware).
- Policy checks at controller level for ownership and destructive actions.
- Server-side validation for every write endpoint.

---

## 4. Required page inventory (complete)

### 4.0 Implementation status (April 2026, this repository)

Use this snapshot to track **member-facing web** (`Blade` + **Tailwind/Vite**, session auth). Staff operations remain under **`/admin/*`** (separate from this inventory’s “client panel” scope unless noted).

| Area | Status | Notes |
|------|--------|--------|
| §4.1 Public | **Done** | Marketing UI, public-safe session/trainer/gym discovery; error pages `403`, `404`, `500`, `503`. |
| §4.2 Auth (members) | **Done** | Routes under `/auth/*` (login, register, OTP, forgot/reset); `POST /logout`; staff login remains `/login`. |
| §4.3 App (common) | **Mostly done** | App shell with sidebar: `/app/dashboard`, `/app/profile` (API-aligned update), `/app/settings` (password). **Notifications:** UI placeholder only — no persisted inbox/API yet (push/OneSignal remains mobile-oriented). |
| §4.4 Sessions & booking | **Done** (web MVP) | `/app/sessions` (search/sort/paginate), detail, **Book** (date + slot) via `BookingsController::createBookings`, bookmark toggle; `/app/bookings` (tabs) + detail. Paid sessions → pending until Stripe web checkout exists. |
| §4.5–4.11 | **Partial / not started** | §4.5 payments; §4.6 bookmarks list page (toggle on session only); workouts, nutrition, social, trainer/gym `/app/*` areas — **next priorities** after payments. |

**Implemented web routes (member app, `auth` + `customer` middleware):**  
`/app/dashboard`, `/app/profile` (POST update), `/app/settings`, `POST /app/settings/password`, `/app/notifications`,  
`/app/sessions`, `/app/sessions/{id}`, `/app/sessions/{id}/book`, `POST /app/sessions/{id}/bookings`, `POST /app/sessions/{id}/bookmark`,  
`/app/bookings`, `/app/bookings/{booking}`.  
**Middleware:** `customer` (`EnsureCustomerPortal`) blocks `admin` from member app (redirect to `admin.dashboard`).

---

## 4.1 Public pages

**Status: implemented**

- `Home / Landing`
- `About`
- `Pricing / Plans`
- `Contact`
- `Sessions Explore` (public-safe listing)
- `Session Detail` (public-safe)
- `Trainers List`
- `Trainer Public Profile`
- `Gyms List`
- `Gym Public Profile`
- `404`, `403`, `500`, maintenance page

## 4.2 Authentication pages

**Status: implemented (members)**

- `Login`
- `Signup`
- `OTP Verification`
- `Forgot Password`
- `Reset Password`
- `Logout` (action/redirect)

## 4.3 Authenticated common pages

**Status: implemented (except notifications data)**

- `Dashboard`
- `My Profile`
- `Settings` (security/preferences)
- `Notifications Center` (placeholder UI; full inbox pending API/storage)

## 4.4 Sessions and booking pages

**Status: implemented (authenticated `/app`; public marketing browse remains on `/sessions`, `/sessions/{id}`)**

- `Sessions Listing` — search `q`, sort (newest / price / title), paginated; published sessions only (`is_publish = 1`).
- `Session Detail` — host, price, duration, rating, timing, description; bookmark + link to book.
- `Book Session` — date + time slot (suggestions from `session_timing` / `schedule` when present); creates booking via same rules as API (`BookingsController::createBookings`); free sessions confirm immediately, paid stay **pending** until payment.
- `My Bookings` — tabs: upcoming / past / cancelled; role-aware (`user` → own bookings; `trainer` / `gym` → bookings where they are assigned).
- `Booking Detail` — session link, payment record when `payment_id` is set.

**Not in this slice:** dedicated bookmarked-sessions list (§4.6), Stripe checkout on web (§4.5).

## 4.5 Payment pages

**Status: not implemented (web)**

- `Checkout / Payment`
- `Payment Status` (success/failed/pending)
- `Payment History`
- `Payment Detail / Receipt`

## 4.6 Bookmark and plan pages

**Status: not implemented (web)**

- `Bookmarked Sessions`
- `Active Plans`
- `Plan Detail`

## 4.7 Workout pages

**Status: not implemented (web)**

- `Workout Dashboard`
- `Workout Plans List`
- `Workout Plan Detail`
- `Create/Edit Workout Plan`
- `Workout Logs List`
- `Workout Log Detail`
- `Create Workout Log`
- `Exercise Logs / Progress`

## 4.8 Nutrition pages

**Status: not implemented (web)**

- `Nutrition Dashboard`
- `Meals by Date`
- `Add/Edit Meal`
- `Nutrition Targets`
- `Adherence / Trend View`

## 4.9 Social pages

**Status: not implemented (web)**

- `Feed`
- `Post Detail`
- `Create Post`
- `Edit Post`
- `My Posts`
- `Status/Story Viewer`
- `Followers`
- `Following`
- `User Public/Social Profile`

## 4.10 Trainer-only pages

**Status: not implemented (web)** — admin has overlapping data tools under `/admin/*`; member trainer UX not built.

- `Trainer Dashboard`
- `My Sessions (CRUD)`
- `Session Create/Edit`
- `Trainer Bookings`
- `Trainer Earnings Overview` (if endpoint data available)
- `Trainer Availability`
- `My Clients`

## 4.11 Gym-only pages

**Status: not implemented (web)** — same note as §4.10 for `/admin` vs `/app/gym/*`.

- `Gym Dashboard`
- `Gym Sessions (CRUD)`
- `Gym Bookings`
- `Gym Trainers`
- `Gym Members`
- `Gym Payment Summary`

---

## 5. Information architecture and route map

### 5.1 Route groups

- Public: `/`
- Auth: `/auth/*`
- App shell: `/app/*`
- Role buckets:
  - `/app/user/*`
  - `/app/trainer/*`
  - `/app/gym/*`

### 5.2 Minimum route skeleton

**Implemented (member app shell):**

- `/app/dashboard`
- `/app/profile` (+ `POST` profile update)
- `/app/settings` (+ `POST` `/app/settings/password`)
- `/app/notifications`
- `/app/sessions`, `/app/sessions/{id}`, `/app/sessions/{id}/book`, `POST …/bookings`, `POST …/bookmark`
- `/app/bookings`, `/app/bookings/{booking}`

**Planned (not wired yet):**

- `/app/payments`, `/app/payments/{id}`
- `/app/bookmarks`
- `/app/workouts/*`
- `/app/nutrition/*`
- `/app/social/*`

**Auth (members):** `/auth/login`, `/auth/register`, `/auth/verify-otp`, `/auth/forgot-password`, `/auth/reset-password`.  
**Staff:** `/login` (admin session).  
**Public marketing:** `/`, `/about`, `/pricing`, `/contact`, `/sessions`, `/sessions/{id}`, `/trainers`, `/trainers/{id}`, `/gyms`, `/gyms/{id}`.

---

## 6. API integration matrix (web module -> backend controller)

- Auth and account:
  - `AuthController`
  - `PasswordResetController`
  - `UserController`
- Session discovery and detail:
  - `SessionsController`
  - `SessionFilterController`
  - `GymsController`
- Booking and payment:
  - `BookingsController`
  - `PaymentController`
  - Stripe webhook (backend processing)
- Bookmarks and active plans:
  - `SessionsController` bookmark/active plan endpoints
- Workouts and exercises:
  - `ExerciseController`
  - `WorkoutPlanController`
  - `WorkoutLogController`
- Nutrition:
  - `NutritionController`
- Social:
  - `PostController`
  - `CommentController`
  - `LikeController`
  - `TagController`
  - `StatusController`
  - `FollowController`
- Notifications and chat:
  - `NotificationController`
  - `GetStreamController`

---

## 7. Data contracts and formatting standards

- Keep payload keys exactly as API expects.
- Do not rename server fields in outbound requests.
- Time-range format standard:
  - `07:00am - 08:30am`
- Date format:
  - Use consistent ISO transport where endpoint accepts it.
- Numeric fields:
  - Validate and normalize client-side before submit.
- JSON arrays:
  - Preserve expected array structure and order where meaningful.

---

## 8. Frontend architecture and reusable building blocks

### 8.1 Stack strategy

- **Current:** Blade + **Tailwind CSS** (Vite) + minimal vanilla JS for toggles; member forms use server-side validation and CSRF; profile updates delegate to existing **API controllers** where noted for parity.
- **Original note:** jQuery was considered for Phase 1; the codebase uses Tailwind/DaisyUI-style patterns on public/auth/app instead.
- Optional phase 2+: Livewire/Inertia if server-driven UI needs to scale.

### 8.2 Shared web components (must build once)

- App shell layout (sidebar + top bar + breadcrumbs) — **partial:** `layouts/app.blade.php` (sidebar + top bar; breadcrumbs optional later)
- Filter bar component
- Reusable paginated table
- Card-list alternative for small screens
- Status badge system
- Toast/error renderer
- Confirm dialog for destructive actions
- Empty/loading/error state blocks

### 8.3 Shared JS utilities

- API request wrapper (headers, error normalization)
- Query-string state helper (filters/pagination persistence)
- Time/date formatting helper
- Currency formatter

---

## 9. UX quality bar

- Responsive behavior at common breakpoints.
- Keyboard and accessibility basics (focus state, labels, form errors).
- Multi-step flows provide progress and validation feedback.
- No dead ends: every page has clear next action.
- Permission-denied screens must explain why and where to go next.

---

## 10. Security, privacy, and compliance checklist

- CSRF on all form and AJAX writes.
- Role middleware and policy checks for all protected routes.
- Prevent IDOR (ownership checks on every resource detail/update/delete).
- Rate-limit auth and abuse-prone endpoints.
- Mask sensitive payment data in UI/logs.
- Add/keep audit trail on critical write actions.

---

## 11. Testing strategy (full-proof)

### 11.1 Critical flow tests (must automate)

- Signup -> OTP -> login -> logout
- Session search -> detail -> booking -> payment -> booking history
- Bookmark add/remove lifecycle
- Workout create/edit/log flow
- Nutrition meal/target update flow
- Social post/comment/like lifecycle

### 11.2 Manual QA checklist

- Cross-role access checks (`user`, `trainer`, `gym`)
- Form validation and API error rendering parity
- Time format consistency in forms/lists/details
- Responsive checks on mobile browser widths
- Empty states and network failure behavior

---

## 12. Delivery phases with acceptance gates

## Phase 0 - Foundation

- Finalize route groups, middleware, base app shell. — **Largely done:** `/auth/*`, `/app/*` with `auth` + `customer`, public routes, marketing + auth layouts.
- Implement shared UI components and API helper utilities. — **Partial:** app shell + public/auth styling; reusable filter/table/toast utilities still open.
- Gate: app shell + auth-protected navigation works for all roles. — **Partial:** member + staff paths work; **role buckets** `/app/user/*`, `/app/trainer/*`, `/app/gym/*` **not created yet**.

## Phase 1 - Revenue-critical MVP

- Auth/account — **Done (web members)**; aligns with API signup/OTP/login/password reset.
- Sessions browse/detail — **Done:** public marketing + **authenticated** `/app/sessions` (search/sort) and detail.
- Booking flow — **Done (web MVP):** book form calls API booking create; paid sessions pending until checkout.
- Payment flow — **Not done (web)** — Stripe checkout UI + webhook-driven status still required for paid sessions.
- Booking history — **Done (web):** `/app/bookings` + detail; full payment history pages still §4.5.
- Gate: end-to-end booking and payment on web works (payment piece outstanding).

## Phase 2 - Engagement and retention

- Bookmarks and active plans
- Workouts module
- Nutrition module
- Gate: daily active use journey possible without mobile app.

## Phase 3 - Community features

- Social feeds/posts/comments/likes/tags/status/follows
- Notifications center — **Web placeholder page only**; real inbox needs product/API support.
- Chat bootstrap page
- Gate: full community interactions available on web.

## Phase 4 - Hardening and scale

- Role and policy audit
- Performance optimization
- Accessibility pass
- E2E smoke suite + production readiness checklist
- Gate: no high/critical issues open for launch scope.

---

## 13. Risks and mitigation

- **Risk:** API/web behavior drift.
  - **Mitigation:** central request builder + shared validation contracts.
- **Risk:** role leaks (unauthorized actions).
  - **Mitigation:** policy tests + route middleware audit.
- **Risk:** payment UX confusion.
  - **Mitigation:** explicit status pages and webhook-driven state refresh.
- **Risk:** schedule/time format inconsistency.
  - **Mitigation:** single formatter helper + strict input masking.
- **Risk:** delivery sprawl.
  - **Mitigation:** phase gates and clear out-of-scope list per release.

---

## 14. Definition of done

- Web app covers core mobile user journeys for all target roles.
- Role-based security and ownership checks are verified.
- Booking/payment flows are reliable and supportable.
- Key operational metrics and logs are observable.
- Documentation exists for QA, support, and future development.

---

## 15. Immediate next execution tasks

1. **Phase 1 — payments:** checkout UI, Stripe payment intent/confirm flow, **payment status** + **history/detail** pages; link pending web bookings to completed payments.
2. **Phase 1 — bookmarks list:** optional `/app/bookmarks` if product wants parity with mobile saved sessions (bookmark toggle already on session detail).
3. **API helper layer (optional but recommended):** thin wrapper for Sanctum-from-web or consistent server-side calls to booking/payment endpoints; keep payload keys identical to API.
4. **Role buckets:** introduce `/app/trainer/*` and `/app/gym/*` (or middleware variants) when building trainer/gym dashboards; keep `customer` vs admin separation.
5. **Notifications:** define storage/API for in-app notification list if product requires parity with mobile; until then keep placeholder or hide nav item.

---

*This is the canonical full-proof plan for web client panel delivery in this repository. Last updated: April 2026 — `/app/sessions` + `/app/bookings` shipped.*
