# Social Network implementation plan

The frontend uses React + TypeScript + MUI. The backend uses PHP with SQLite for development persistence. Vite, Storybook, Vitest and Playwright are development/testing tooling.

## Foundation
- [x] Repository and base documentation
- [x] React + TypeScript frontend
- [x] MUI design system and responsive application shell
- [x] PHP API + SQLite development persistence
- [ ] Production environment configuration
- [x] Database migration and seed scripts
- [ ] Remove runtime schema bootstrap in favor of migrations

## Social features
- [x] Registration/login/logout
- [x] Profiles, bio and avatar
- [x] Feed and posts
- [x] Post creation, editing and deletion
- [x] Likes/reactions foundation
- [x] Comments
- [x] Comment editing and deletion
- [x] Stories, viewer and seen state
- [x] User search/listing
- [x] Private messaging foundation
- [x] Follow/friend relationships
- [x] Notifications and read state
- [x] Image/file upload foundation
- [x] Pagination/load more
- [x] Account, privacy and notification settings
- [ ] Public user profiles and follow UI
- [ ] Infinite scroll
- [ ] Rich reaction history/counts by reaction type
- [ ] Media galleries and post attachment management

## Architecture and quality
- [ ] Feature-based React folders and reusable MUI components
- [ ] PHP controllers/services/repositories/validators
- [ ] Database migration lifecycle
- [ ] Centralized API validation and authorization
- [x] CSRF protection for all state-changing HTTP methods
- [ ] Secure session cookie configuration
- [ ] Rate limiting and abuse protection
- [ ] Complete security header policy including CSP
- [ ] Accessibility audit
- [ ] Meaningful unit coverage for API client and components
- [ ] Full E2E authentication/social flows
- [x] Storybook foundation
- [ ] Storybook component catalog
- [x] CI gates for TypeScript, frontend tests, build, Storybook and PHP syntax
- [ ] E2E backend startup and authenticated social-flow coverage
- [ ] Production deployment documentation
