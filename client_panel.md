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

## 4.1 Public pages

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

- `Login`
- `Signup`
- `OTP Verification`
- `Forgot Password`
- `Reset Password`
- `Logout` (action/redirect)

## 4.3 Authenticated common pages

- `Dashboard`
- `My Profile`
- `Settings` (security/preferences)
- `Notifications Center`

## 4.4 Sessions and booking pages

- `Sessions Listing` (search/filter/sort)
- `Session Detail` (full metadata)
- `Book Session` (date/slot selection)
- `My Bookings` (tabs: upcoming/past/cancelled)
- `Booking Detail`

## 4.5 Payment pages

- `Checkout / Payment`
- `Payment Status` (success/failed/pending)
- `Payment History`
- `Payment Detail / Receipt`

## 4.6 Bookmark and plan pages

- `Bookmarked Sessions`
- `Active Plans`
- `Plan Detail`

## 4.7 Workout pages

- `Workout Dashboard`
- `Workout Plans List`
- `Workout Plan Detail`
- `Create/Edit Workout Plan`
- `Workout Logs List`
- `Workout Log Detail`
- `Create Workout Log`
- `Exercise Logs / Progress`

## 4.8 Nutrition pages

- `Nutrition Dashboard`
- `Meals by Date`
- `Add/Edit Meal`
- `Nutrition Targets`
- `Adherence / Trend View`

## 4.9 Social pages

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

- `Trainer Dashboard`
- `My Sessions (CRUD)`
- `Session Create/Edit`
- `Trainer Bookings`
- `Trainer Earnings Overview` (if endpoint data available)
- `Trainer Availability`
- `My Clients`

## 4.11 Gym-only pages

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

- `/app/dashboard`
- `/app/profile`
- `/app/sessions`, `/app/sessions/{id}`
- `/app/bookings`, `/app/bookings/{id}`
- `/app/payments`, `/app/payments/{id}`
- `/app/bookmarks`
- `/app/workouts/*`
- `/app/nutrition/*`
- `/app/social/*`
- `/app/settings`

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

- Phase 1: Blade + jQuery (fastest fit with current project)
- Optional phase 2+: progressive migration to Livewire/Inertia if needed

### 8.2 Shared web components (must build once)

- App shell layout (sidebar + top bar + breadcrumbs)
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

- Finalize route groups, middleware, base app shell.
- Implement shared UI components and API helper utilities.
- Gate: app shell + auth-protected navigation works for all roles.

## Phase 1 - Revenue-critical MVP

- Auth/account
- Sessions browse/detail
- Booking flow
- Payment flow
- Booking/payment history
- Gate: end-to-end booking and payment on web works.

## Phase 2 - Engagement and retention

- Bookmarks and active plans
- Workouts module
- Nutrition module
- Gate: daily active use journey possible without mobile app.

## Phase 3 - Community features

- Social feeds/posts/comments/likes/tags/status/follows
- Notifications center
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

- Confirm final page priority for Phase 1.
- Create route stubs and nav map for all required pages.
- Implement shared app shell and API helper layer.
- Start MVP sprint with sessions -> bookings -> payments.

---

*This is the canonical full-proof plan for web client panel delivery in this repository.*
