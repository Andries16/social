# Architecture

React is the presentation and interaction layer. Bootstrap provides responsive layout and component styling, with local CSS for product-specific presentation.

PHP is the HTTP/API layer. It owns authentication, authorization, validation and persistence. SQLite is the development persistence layer.

The codebase is organized around separation of responsibilities. React components should remain focused on rendering and interaction; API communication is isolated behind a small HTTP boundary; PHP route handlers should delegate domain behavior to services as the project grows.

SOLID goals:
- Single Responsibility: each component/service has one reason to change.
- Open/Closed: new features are isolated rather than modifying unrelated behavior.
- Liskov Substitution: stable data contracts are used at boundaries.
- Interface Segregation: APIs expose focused operations.
- Dependency Inversion: UI depends on HTTP contracts, not persistence details.

Future production work should move the current bootstrap schema into versioned migrations and extract PHP controllers/services/repositories without changing the public API contract.