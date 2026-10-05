import { defineConfig } from "@playwright/test";

export default defineConfig({
  testDir: "tests/e2e",
  use: {
    baseURL: "http://127.0.0.1:5173",
  },
  webServer: {
    command:
      "php backend/migrate.php && (php -S 127.0.0.1:8080 backend/index.php >/tmp/social-php.log 2>&1 & PHP_PID=$!; trap 'kill $PHP_PID 2>/dev/null || true' EXIT; npm run dev -- --host 127.0.0.1)",
    url: "http://127.0.0.1:5173",
    reuseExistingServer: true,
  },
});
