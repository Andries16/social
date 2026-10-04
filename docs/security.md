# Security

Passwords use password_hash/password_verify. Authentication is server-side session based.

Before production:
- enable HTTPS and Secure/HttpOnly/SameSite cookies;
- add CSRF tokens for state-changing cookie-authenticated requests;
- validate and normalize every input;
- add authorization checks for every resource mutation;
- add rate limiting and abuse protection;
- validate uploaded files by MIME and content, not extension;
- add Content-Security-Policy and other security headers;
- keep secrets outside the repository;
- never commit backend/social.sqlite.