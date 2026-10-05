<?php declare(strict_types=1);

namespace Social\Http;

final class Bootstrap
{
    public static function run(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (is_string($path) && str_starts_with($path, '/uploads/') && is_file(__DIR__ . '/../../' . $path)) {
            return;
        }

        session_set_cookie_params([
            'httponly' => true,
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'samesite' => 'Lax',
        ]);
        session_start();

        header('Content-Type: application/json');

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowedOrigins = array_values(array_filter(array_map(
            'trim',
            explode(',', getenv('SOCIAL_ALLOWED_ORIGINS') ?: 'http://localhost:5173,http://127.0.0.1:5173')
        )));
        if (in_array($origin, $allowedOrigins, true)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
        header('Access-Control-Allow-Methods: GET,POST,PATCH,DELETE,OPTIONS');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");

        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        $db = new \PDO('sqlite:' . __DIR__ . '/../../social.sqlite');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->exec('PRAGMA foreign_keys=ON');

        require __DIR__ . '/../../routes.php';
    }
}
