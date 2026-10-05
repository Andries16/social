# Configuration

Frontend:
- `VITE_API_URL`: public API origin used by the browser.

Backend:
- `SOCIAL_ALLOWED_ORIGINS`: comma-separated exact origins allowed by CORS.
- PHP session security automatically enables the Secure cookie flag when HTTPS is detected.

Development defaults are safe for localhost. Production deployments must provide explicit frontend/API origins and HTTPS.
