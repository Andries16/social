# Configuration

The application is configured through environment variables rather than hard-coded deployment values.

## Frontend

- `VITE_API_URL`: public base URL of the PHP API. Defaults to `http://localhost:8080` for local development.

## API

- `SOCIAL_ALLOWED_ORIGINS`: comma-separated browser origins allowed by the API CORS policy. Example: `https://social.example.com`.
- `SOCIAL_PUBLIC_URL`: canonical public base URL used when generating uploaded-media URLs. Set this in production so media links do not depend on the incoming HTTP Host header.

## Local development

The default values support the Vite frontend on `http://localhost:5173` and `http://127.0.0.1:5173`, with the PHP API on `http://localhost:8080`.

## Production

Set all public-facing URLs explicitly, use HTTPS, and keep `SOCIAL_ALLOWED_ORIGINS` restricted to trusted frontend origins. Persist the SQLite database and `backend/uploads/` directory outside ephemeral application storage.
