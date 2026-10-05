# Security

Passwords use `password_hash`/`password_verify`. Authentication is server-side session based. Mutating API routes require the session CSRF token, including DELETE requests, and resource mutations enforce ownership where applicable. Private-account read authorization is enforced for profiles, feed posts and stories.

Before production:
- enable HTTPS and configure Secure, HttpOnly and SameSite session cookies;
- restrict CORS to an explicit trusted-origin allowlist; the API reads `SOCIAL_ALLOWED_ORIGINS` in production and defaults to local Vite origins for development;
- validate and normalize every input with consistent server-side constraints;
- add authorization/privacy checks for reads as well as mutations;
- add rate limiting and abuse protection for authentication, messaging and uploads;
- validate uploaded files by detected MIME type and enforce size/quota limits;
- add Content-Security-Policy and a complete security-header policy;
- keep secrets and production database configuration outside the repository;
- never commit `backend/social.sqlite` or uploaded media;
- run database migrations as a deployment step rather than relying on runtime schema creation.
