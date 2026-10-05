# Architecture

The application is split into a typed React presentation layer and a PHP HTTP/API layer.

## Frontend
- React + TypeScript
- MUI as the component/design library
- Vite for development/build tooling
- Feature/page separation with reusable components
- services/api.ts owns HTTP communication
- theme.ts owns MUI design tokens and component defaults

Target structure:
src/
  components/
  features/
  hooks/
  pages/
  services/
  state/
  theme.ts
  types.ts

## Backend
PHP remains the application API and SQLite is the development persistence layer. The HTTP bootstrap is isolated in `backend/src/Http/Bootstrap.php`, while the route implementation is currently isolated in `backend/routes.php`. This extraction is intentionally behavior-preserving; the next refactor stages can move route groups into controllers/services/repositories without changing the public API.

## SOLID
- Single Responsibility: components, services and controllers have one reason to change.
- Open/Closed: UI behavior is extended through composition and reusable MUI components.
- Liskov Substitution: typed component props preserve substitutability.
- Interface Segregation: API types expose only data required by each feature.
- Dependency Inversion: React pages depend on the typed API service rather than fetch directly.

## Responsive design
MUI breakpoints are the primary responsive mechanism. Layouts must work from narrow mobile screens through desktop widths.
