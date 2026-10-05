# Development

Frontend:
- npm install
- npm run dev
- npm run storybook

Backend:
- php backend/migrate.php
- php -S localhost:8080 backend/index.php
- php backend/seed.php

Run migrations before starting the API. Runtime schema creation is intentionally disabled; deployments must execute database migrations explicitly.

The application uses React + TypeScript + MUI. PHP provides the API and SQLite is the development database.

Demo accounts created by the seed script use password `password`. Never use seeded credentials in production.
