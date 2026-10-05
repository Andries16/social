# Accessibility audit

The UI follows these baseline rules:
- Interactive controls use native MUI buttons, links, or explicit keyboard handlers.
- Icon-only controls have accessible labels.
- Forms use visible labels and required fields.
- Story items are keyboard reachable and activate on Enter or Space.
- Images supplied by users use empty alternative text when decorative; generated profile images expose the user's initial as fallback content.
- Color is not the only interaction/status indicator.
- Responsive layouts preserve keyboard access and content order.

Before each release, run a keyboard-only pass through authentication, feed creation, post actions, comments, story viewer, profile, settings, messaging, search, and notifications. Automated browser coverage should be extended with axe-based checks when the production test stack includes an accessibility scanner.
