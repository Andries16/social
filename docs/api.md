# API

Base URL: `http://localhost:8080` in development.

Authentication:
- POST /api/register
- POST /api/login
- POST /api/logout
- GET /api/me
- PATCH /api/me
- GET /api/settings
- PATCH /api/settings

Profiles and social graph:
- GET /api/users?q=&limit=
- GET /api/users/:id
- GET /api/users/:id/follow
- POST /api/users/:id/follow
- DELETE /api/users/:id/follow
- GET /api/users/:id/followers
- GET /api/users/:id/following

Feed:
- GET /api/posts?page=&limit=
- POST /api/posts
- PATCH /api/posts/:id
- DELETE /api/posts/:id
- POST /api/posts/:id/react
- POST /api/posts/:id/comments
- PATCH /api/comments/:id
- DELETE /api/comments/:id

Stories:
- GET /api/stories
- POST /api/stories
- POST /api/stories/:id/view

Messaging and notifications:
- GET /api/messages?user_id=:id
- POST /api/messages
- GET /api/notifications
- POST /api/notifications/:id/read

Media and health:
- POST /api/media
- GET /health.php

All authenticated endpoints require the PHP session cookie. POST/PATCH/DELETE requests also require the CSRF token returned by /api/me, /api/login, or /api/register.

Pagination:
- Feed endpoints return `page`, `posts`, and `hasMore`.
- The frontend uses cursor-like page progression and an IntersectionObserver with a manual fallback.

Privacy:
- Private profiles expose limited profile information until followed.
- Private-account posts and stories are only returned to the owner or an accepted follower.
