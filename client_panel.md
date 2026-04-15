# Client Panel (Web) Build Plan

This document defines everything needed to build a **web-based client panel** using the existing API-first backend (currently focused on mobile app usage).

Goal: deliver a browser UI where end users can do what they do in the mobile app, plus web-specific usability improvements.

---

## 1) Objectives

- Build a responsive client web app for `user`, `trainer`, and `gym` roles.
- Reuse existing API endpoints and validation rules wherever possible.
- Avoid business-logic duplication in Blade/JS; keep logic in controllers/services/API.
- Launch in phases so high-impact workflows go live first.

---

## 2) Target roles and access

- **Client/User**
  - Account/profile management
  - Session discovery, details, booking, payment
  - Bookmarks, active plans, workouts, nutrition, social activity
- **Trainer**
  - Own session list and session creation/edit
  - Booking and schedule visibility
  - Content posting and profile management
- **Gym**
  - Gym-facing session and booking operations
  - Payment and member visibility

Use role-based middleware and policy checks on all web routes.

---

## 3) Core web modules to build

## 3.1 Authentication and account

- Login, signup, OTP verify, forgot/reset password
- Session persistence in browser (Sanctum token handling)
- Logout and device/session invalidation UX
- Profile edit page (name, avatar, personal metrics, trainer specialties, etc.)

API base:
- `AuthController`
- `PasswordResetController`
- `UserController`

## 3.2 Discover sessions and trainers

- Sessions listing page with filters (goal, duration, intensity, type, keyword)
- Session detail page (media, trainer, timing, price, fitness metadata)
- Trainers listing/profile and optional “find buddy” experience
- Gym listing page

API base:
- `SessionsController`
- `SessionFilterController`
- `UserController` (trainers / users)
- `GymsController`

## 3.3 Booking and payments

- Create booking flow from session detail
- Date/time selection UX (human-friendly time picker)
- Booking history page with status filters
- Payment intent + confirmation flow
- Payment history page and invoice-like receipt view

API base:
- `BookingsController`
- `PaymentController`
- Stripe webhook flow (backend already present)

## 3.4 Bookmarks and active plans

- Save/remove bookmarked sessions
- Bookmarks listing page
- Active plans screen for user/trainer/gym context

API base:
- `SessionsController` bookmark and active-plan endpoints

## 3.5 Workouts and exercise logs

- Exercise categories and exercises browse
- Workout plans list/detail/create/edit
- Workout logs create and history timeline
- Exercise-level logging UI

API base:
- `ExerciseController`
- `WorkoutPlanController`
- `WorkoutLogController`

## 3.6 Nutrition

- Meals CRUD by date and meal type
- Daily macro summary widget
- Nutrition targets view/update
- Adherence trends and simple charts

API base:
- `NutritionController`

## 3.7 Social and engagement

- Feed page: posts list/detail
- Create/edit/delete post (role-dependent)
- Comment and like/dislike actions
- Tag usage in post create flow
- Status media upload and viewer
- Follow/unfollow and followers/following pages

API base:
- `PostController`
- `CommentController`
- `LikeController`
- `TagController`
- `StatusController`
- `FollowController`

## 3.8 Notifications and chat

- Notification center page (read/list actions if supported)
- Push/notification preference toggles (if API coverage allows)
- Chat token/channel bootstrap for web chat integration

API base:
- `NotificationController`
- `GetStreamController`

---

## 4) Recommended web route structure

- `/` landing page (public)
- `/auth/*` auth routes
- `/app/dashboard`
- `/app/profile`
- `/app/sessions`, `/app/sessions/{id}`
- `/app/bookings`
- `/app/payments`
- `/app/bookmarks`
- `/app/workouts/*`
- `/app/nutrition/*`
- `/app/social/*`
- `/app/settings`

Use route groups by auth + role.

---

## 5) UI/UX requirements (web-specific)

- Fully responsive layout (desktop/tablet/mobile browser)
- Persistent sidebar + top nav for logged-in users
- Table + card hybrid views (tables on desktop, cards on mobile)
- Global filters/search/sort with URL query-state persistence
- Reusable modal/drawer components
- Form validation:
  - client-side precheck
  - API error rendering (field-level + toast)
- Loading skeletons and empty states for each module
- Clear permission-denied and not-found pages

---

## 6) Technical requirements

- Keep current stack friendly:
  - Blade + jQuery can be used first for fast delivery
  - Optional gradual move to Livewire/Inertia later
- Centralized API client wrapper for:
  - auth header
  - error normalization
  - retry/refresh behavior
- Shared helpers for:
  - time/date formatting
  - currency formatting
  - pagination query handling
- Standardized component partials:
  - filter bar
  - paginated table
  - detail modal
  - status badges

---

## 7) Data and validation parity checklist

- Keep API as source of truth for validation and business rules.
- Web forms must submit payloads in exact API format.
- For time-range fields (example session timing), format consistently:
  - `07:00am - 08:30am`
- Preserve existing DB schema behavior unless explicitly migrated.
- Avoid writing role-specific logic in view templates.

---

## 8) Security and access control

- Enforce role middleware on all `/app/*` routes.
- Policy checks for update/delete operations.
- CSRF protection for web forms and AJAX requests.
- Rate-limit auth-sensitive and write-heavy endpoints.
- Audit critical actions (booking/payment/content moderation).

---

## 9) Monitoring and operational readiness

- Error logging with route + user context
- API latency and failure dashboard (at least basic logs)
- Stripe payment failure visibility in panel
- Optional export tools (CSV for bookings/payments/workouts)

---

## 10) Delivery phases (suggested)

## Phase A (MVP - High priority)

- Auth + profile
- Sessions browse/detail
- Booking create/history
- Payment flow
- Basic dashboard

## Phase B

- Bookmarks + active plans
- Workout plans/logs
- Nutrition module

## Phase C

- Social (posts/comments/likes/tags/status/follows)
- Notifications + chat bootstrap
- Reporting/export polish

## Phase D (Hardening)

- Role/policy audit
- UX polish and accessibility pass
- E2E smoke tests on critical flows

---

## 11) Testing matrix (must-have)

- Signup/login/logout and token expiry behavior
- Session search/filter and detail load correctness
- Booking + payment happy path and failure path
- Time formatting consistency across forms/details/lists
- Workout/nutrition CRUD flows
- Social actions with role checks
- Mobile browser responsive checks

---

## 12) Definition of done (web client panel)

- Key user journeys are executable on browser without mobile app dependency.
- All major API modules used by app have equivalent web workflows.
- Role-based access is enforced and tested.
- No critical blocker in booking/payment/session workflows.
- Documentation and runbook for support team is available.

---

*This file is a web-client implementation blueprint based on the current API-first architecture.*
