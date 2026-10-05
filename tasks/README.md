# Social Network implementation plan

The frontend uses React + TypeScript + MUI. The backend uses PHP with SQLite for development persistence. Vite, Storybook, Vitest and Playwright are development/testing tooling.

## Foundation
- [x] Repository and base documentation
- [x] React + TypeScript frontend
- [x] MUI design system and responsive application shell
- [x] PHP API + SQLite development persistence
- [x] Production environment configuration
- [x] Database migration and seed scripts
- [x] Remove runtime schema bootstrap in favor of migrations

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
- [x] Public user profiles and follow UI
- [x] Infinite scroll
- [x] Rich reaction history/counts by reaction type
- [x] Media galleries and post attachment management

## Architecture and quality
- [x] Feature-based React folders and reusable MUI components
- [x] PHP controllers/services/repositories/validators (HTTP bootstrap, domain services/repositories, media storage and route adapters are separated)
- [x] Database migration lifecycle
- [ ] Centralized API validation and authorization (privacy-aware read authorization is now enforced on profiles, feed and stories; input bounds are now enforced server-side)
- [x] CSRF protection for all state-changing HTTP methods
- [x] Secure session cookie configuration
- [x] Rate limiting and abuse protection
- [x] Complete security header policy including CSP
- [x] Accessibility audit
- [x] Meaningful unit coverage for API client and components
- [x] Full E2E authentication/social flows
- [x] Storybook foundation
- [x] Storybook component catalog
- [x] CI gates for TypeScript, frontend tests, build, Storybook and PHP syntax
- [x] E2E backend startup and authenticated social-flow coverage
- [x] Production deployment documentation
