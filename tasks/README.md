# Social Network implementation plan

The frontend uses React + TypeScript + MUI. The backend uses PHP with SQLite for development persistence. Vite, Storybook, Vitest and Playwright are development/testing tooling.

## Foundation
- [x] Repository and base documentation
- [x] React + TypeScript frontend
- [x] MUI design system and responsive application shell
- [x] PHP API + SQLite development persistence
- [ ] Production environment configuration
- [ ] Database migrations and seed command

## Social features
- [x] Registration/login/logout
- [x] Profiles, bio and avatar URL
- [x] Feed and posts
- [x] Likes/reactions foundation
- [x] Comments
- [x] Stories foundation
- [x] User listing
- [x] Private messaging foundation
- [x] Follow/friend relationships
- [x] Notifications
- [ ] Real image/file uploads and profile photo uploads
- [ ] Post editing/deletion
- [ ] Comment editing/deletion
- [ ] Rich reaction picker
- [x] Story viewer and seen state
- [ ] Search
- [ ] Pagination/infinite scroll
- [ ] Account, privacy and notification settings

## Architecture and quality
- [ ] Feature-based React folders and reusable MUI components
- [ ] PHP controllers/services/repositories/validators
- [ ] Database migrations
- [ ] API validation and authorization
- [ ] CSRF protection and secure session configuration
- [ ] Rate limiting and security headers
- [ ] Accessibility audit
- [ ] Unit coverage for API contracts and components
- [ ] Full E2E authentication/social flows
- [x] Storybook foundation
- [ ] Storybook component catalog
- [ ] CI quality gates for TypeScript, PHP, tests and Storybook
- [ ] Production deployment documentation
