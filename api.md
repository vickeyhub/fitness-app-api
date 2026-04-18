# Fitness App API

This document describes the HTTP API defined in `routes/api.php`. Laravel registers these routes under the **`/api`** prefix (for example, `POST /api/login`).

## Base URL

Use your deployed origin plus `/api`, for example:

- Local: `http://localhost:8000/api` (or your `APP_URL` + `/api`)

## Authentication

Most endpoints require **Laravel Sanctum** bearer tokens.

- **Header:** `Authorization: Bearer <token>`
- **Content-Type:** `application/json` for JSON bodies (unless uploading files; use `multipart/form-data` where noted).

Tokens are issued on successful **login** (and related flows). Send the token on every request inside the `auth:sanctum` group below.

Unauthenticated requests to protected routes receive **401** (see the named `login` JSON route in `api.php`).

---

## Public routes (no bearer token)

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/signup` | Register; sends OTP flow for email verification |
| `POST` | `/verify-otp` | Verify OTP after signup |
| `POST` | `/login` | Login; returns user and Sanctum token |
| `POST` | `/forgot-password` | Request password reset |
| `POST` | `/reset-password` | Complete password reset |
| `GET` | `/gyms` | List/search gyms |
| `POST` | `/search-sessions` | Search sessions/classes |
| `GET` | `/session-detail/{id}` | Session detail by ID |
| `POST` | `/stripe/webhook` | Stripe webhook (server-to-server; not for mobile clients) |
| `POST` | `/notification` | Notification hook (see `NotificationController`) |

---

## Protected routes (`auth:sanctum`)

All paths below are relative to `/api` and require `Authorization: Bearer <token>`.

### Auth & profile

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/logout` | Revoke current token / logout |
| `GET` | `/user/profile` | Current user profile |
| `POST` | `/user/profile` | Update profile |
| `GET` | `/users` | List users (`fetchAllUsers`) |
| `POST` | `/find-buddy` | Find a buddy |
| `POST` | `/trainers` | Get trainers |

### Bookings & sessions

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/bookings` | Create booking(s) |
| `GET` | `/bookings` | List bookings |
| `GET` | `/sessions` | List sessions |
| `DELETE` | `/sessions/{id}` | Delete session |
| `POST` | `/sessions` | Create session |
| `POST` | `/bookmark` | Save bookmark |
| `GET` | `/bookmark` | Bookmarked sessions |
| `GET` | `/session/session-filter-api` | Filter data for sessions |
| `GET` | `/fetch-active-plans` | Active plans |
| `GET` | `/owner-bookings` | Owner: bookings from users (gyms) |

### Payments (Stripe)

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/create-payment-intent` | Create Stripe payment intent |
| `POST` | `/confirm-payment` | Confirm payment |

### Social: follow

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/follow/{user}` | Follow user |
| `DELETE` | `/unfollow/{user}` | Unfollow user |
| `GET` | `/following` | Users you follow |
| `GET` | `/followers/{user}` | Followers of user |
| `GET` | `/is-following/{user}` | Whether you follow user |

### Status stories

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/status/upload` | Upload status (typically multipart) |
| `GET` | `/status/feed` | Status feed |
| `GET` | `/status/me` | Current user statuses |
| `DELETE` | `/status/{id}` | Delete status |

### Nutrition (`/nutrition` prefix)

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/nutrition/add` | Add or update meal |
| `GET` | `/nutrition/` | Nutrition by date (see controller for query params) |
| `DELETE` | `/nutrition/` | Delete meal |
| `POST` | `/nutrition/target/set` | Set nutrition targets |
| `GET` | `/nutrition/target` | Get targets |

### Workouts (`/workout` prefix and related)

| Method | Path | Description |
|--------|------|-------------|
| `GET` | `/workout/categories` | Exercise categories |
| `GET` | `/workout/exercises` | Exercises (e.g. by category) |
| `POST` | `/workout/workout-plans` | Create workout plan |
| `GET` | `/workout/workout-plans` | List workout plans |
| `GET` | `/workout/workout-plans/{id}` | Show workout plan |
| `DELETE` | `/workout/workout-plans/{id}` | Delete workout plan |
| `GET` | `/workouts/history` | Workout history |
| `POST` | `/workout/log` | Log workout |
| `POST` | `/exercises/log` | Log exercise |

### Posts, likes, comments, tags

**Posts** — `Route::apiResource('posts', ...)` maps to:

| Method | Path | Description |
|--------|------|-------------|
| `GET` | `/posts` | List posts |
| `POST` | `/posts` | Create post |
| `GET` | `/posts/{post}` | Show post |
| `PUT`/`PATCH` | `/posts/{post}` | Update post |
| `DELETE` | `/posts/{post}` | Delete post |

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/posts/{post}/like` | Like post |
| `POST` | `/posts/{post}/dislike` | Dislike / unlike |
| `GET` | `/posts/{post}/comment` | List comments |
| `POST` | `/posts/{post}/comment` | Add comment |
| `DELETE` | `/comments/{comment}` | Delete comment |
| `GET` | `/tags` | List tags |
| `POST` | `/tags` | Create tag |

### GetStream (`/getstream` prefix)

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/getstream/token` | Generate chat token |
| `POST` | `/getstream/channel` | Create channel |
| `POST` | `/getstream/register-all-users` | Register users with GetStream |

---

## Implementation notes

- **Request/response bodies** for each action are implemented in `App\Http\Controllers\Api\*`. Refer to the corresponding controller method for required fields, validation rules, and JSON shape.
- **CORS / cookies:** API middleware includes `statefulApi()` in `bootstrap/app.php`; mobile clients typically use **Bearer tokens** only.
- **Webhooks:** Do not call `/stripe/webhook` from the app; configure the URL in the Stripe dashboard.

---

## Quick test (curl)

Replace `BASE` and `TOKEN` as needed.

```bash
# Login
curl -s -X POST "$BASE/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"you@example.com","password":"secret"}'

# Authenticated example
curl -s "$BASE/api/user/profile" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```
