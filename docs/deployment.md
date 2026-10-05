# Production deployment

The application consists of a Vite-built React frontend and a PHP API backed by SQLite for small deployments.

Before deployment:
- Set `VITE_API_URL` to the public API origin.
- Configure `SOCIAL_ALLOWED_ORIGINS` with the exact frontend origins.
- Run `php backend/migrate.php` as a deployment step before starting the API.
- Run `php backend/seed.php` only for development/demo environments.
- Persist `backend/social.sqlite` and `backend/uploads/` outside ephemeral containers.
- Serve the frontend through a production web server and route `/uploads/*` to the backend upload directory.
- Use HTTPS so PHP session cookies are marked Secure.
- Never enable demo credentials or expose the SQLite database publicly.

Recommended production topology:
1. CDN/reverse proxy for the static Vite output.
2. PHP-FPM behind the reverse proxy for `/api/*` and `/health.php`.
3. Persistent volume for SQLite and uploaded media.
4. Scheduled backups of the SQLite database and upload directory.

SQLite is the development persistence target. For higher write concurrency, introduce a production repository implementation backed by PostgreSQL or another transactional database while retaining the domain-service interfaces.
