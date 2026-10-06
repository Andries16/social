# Social

A responsive social-network application built with React, TypeScript, MUI, PHP, and SQLite.

## Features

- Account registration, login, logout, and session-based authentication
- Profiles with bio and profile photo uploads
- Public and private accounts with follow/unfollow
- Feed with pagination and private-account visibility rules
- Posts with image galleries, editing, deletion, comments, and reactions
- 24-hour stories with story viewing
- Direct messaging
- Notifications for follows, reactions, comments, and messages
- User search
- Account and privacy settings
- CSRF protection, rate limiting, upload ownership validation, and security headers
- Storybook component coverage
- Vitest unit/API-client tests
- Playwright end-to-end coverage
- GitHub Actions CI for TypeScript, tests, production build, Storybook, PHP syntax, and E2E

## Stack

Frontend: React, TypeScript, Vite, MUI, Storybook, Vitest, Playwright.

Backend: PHP 8+, PDO, SQLite, session authentication, a domain-oriented service/repository structure.

## Development

Requirements: Node 22, npm 10, PHP 8+, SQLite, and the PHP SQLite extension.

```bash
nvm use
npm install
php backend/migrate.php
npm run dev
```

The frontend runs on `http://localhost:5173`. The development PHP API can be started separately:

```bash
php -S 127.0.0.1:8080 backend/index.php
```

If the API is hosted elsewhere, set `VITE_API_URL`. For browser/API origin configuration, see `docs/configuration.md`.

## Quality checks

```bash
npx tsc --noEmit
npm test
npm run build
npm run build-storybook
npm run e2e
find backend -name "*.php" -print0 | xargs -0 -n1 php -l
```

## Documentation

- [Architecture and project guide](docs/README.md)
- [Configuration](docs/configuration.md)
- [Deployment](docs/deployment.md)
- [Testing](docs/testing.md)
- [Accessibility](docs/accessibility.md)
- [Task roadmap](tasks/README.md)

## Security

Uploaded images are validated by MIME type, size, and generated storage names. Uploaded media is associated with the authenticated owner and cannot be attached to posts or profiles by another user. State-changing API requests require the session CSRF token. Authentication and content-creation endpoints are rate limited.

For production, use HTTPS, persistent storage for SQLite and uploads, restrictive allowed origins, and a production PHP process manager/web server. See the deployment documentation.
