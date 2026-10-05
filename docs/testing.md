# Testing

TypeScript:
- `npx tsc --noEmit`

Unit tests:
- `npm test`
- API client behavior is covered with Vitest.
- Shared UI components have dependency-free server-rendering tests where practical.

Build:
- `npm run build`

Storybook:
- `npm run build-storybook`

Browser E2E:
- `npm run e2e`
- Playwright starts the migrated PHP backend and Vite development server.
- Authentication and an authenticated social flow cover registration, story creation, post creation, and commenting.

PHP:
- `find backend -name "*.php" -print0 | xargs -0 -n1 php -l`
- `php backend/migrate.php` applies migrations in lexical order and records each migration exactly once.
