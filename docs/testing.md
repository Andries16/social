# Testing

Unit tests use Vitest. End-to-end tests use Playwright. Storybook is used for isolated UI development.

Local commands:
```
npm test
npm run e2e
npm run storybook
npm run build-storybook
```

The first E2E smoke test validates that the React application renders its authentication boundary. Additional tests should cover registration, login, post creation, reactions, comments, stories, profile editing and messaging.