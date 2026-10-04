# API

Base URL: `http://localhost:8080`

Authentication:
- POST /api/register
- POST /api/login
- POST /api/logout
- GET /api/me
- PATCH /api/me

Social:
- GET /api/posts
- POST /api/posts
- POST /api/posts/:id/react
- POST /api/posts/:id/comments
- GET /api/stories
- POST /api/stories

People and messaging:
- GET /api/users
- GET /api/messages?user_id=:id
- POST /api/messages

All protected endpoints require the PHP session cookie.